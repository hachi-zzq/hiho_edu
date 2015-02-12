<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Whoops\Example\Exception;
use \Playlist;

/**
 * PlayID 统一播放 ID
 *
 * PlayID 为所有可播放元素的抽象, 包含: 视频、碎片、播放列表
 * 每一个可播放对象均会随机生成一个 PlayID 字符串, 与之原始实体类型+主键对应。
 *
 * 最终向外呈现的 URL 将类似为:
 * http://hiho.com/play/AbCdEfGh
 * http://hiho.com/play/AbCdEfGh?st=xxx&et=xxx (生成碎片)
 * http://hiho.com/play/AbCdEfGh?type=fragment (注释性质的 type)
 * http://hiho.com/play/AbCdEfGh?type=note&start_by=AbCdEfGh (从某处开始 type)
 * http://hiho.com/play/AbCdEfGh?mode=simple (简单模式播放)
 * http://hiho.com/play/AbCdEfGh?mode=learning (学习模式播放)
 *
 * 所有直接的实体 ID 均不会向外部暴露，如播放地址、字幕和评论的生成等。
 * 播放页面会判断 PlayID 所对应的类型和 ID 而加载不同的 View。
 *
 * 字段: ID, EntityType, EntityId, SoftDelete
 *
 * 生成方式: 使用 uniqid() 方法生成唯一的 ID 后，使用 Base62 Encode 运算
 *
 * @package HiHo\Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class PlayID extends \Eloquent
{
    protected $table = 'playid';

    protected $primaryKey = 'play_id';

    public $timestamps = false;

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    protected $fillable = array('play_id', 'entity_type', 'entity_id');

    /**
     * 获得当前 PlayID 对应的 HTTP 播放链接
     * @param $playid
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getPlayUrl()
    {
        if (!$this->entity_id or !$this->entity_type) {
            return NULL;
        }
    }

    /**
     * 获得当前 PlayID 的对应 Model 实体
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getEntity()
    {
        if (!$this->entity_id or !$this->entity_type) {
            return NULL;
        }

        switch ($this->entity_type) {
            case 'VIDEO':
                $entity = Video::find($this->entity_id);
                break;
            case 'FRAGMENT':
                $entity = Fragment::find($this->entity_id);
                break;
            case 'PLAYLIST':
                $entity = Playlist::find($this->entity_id);
                break;
            case 'NOTE':
                $entity = Playlist::find($this->entity_id);
                break;
            default:
                $entity = NULL;
                echo '';
        }

        return $entity;
    }

    /**
     * 使用 Model 实体对象创建新 PlayID
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    static public function createWithEntity($entity)
    {
        // 查找已存在的 PlayID
        $playid = self::isExistWithEntity($entity);

        // 判断实体类型
        $entityType = self::getEntityType($entity);

        if (!$playid) {
            // 判断并存储
            $playid = self::firstOrCreate(
                array(
                    'play_id' => self::mt_rand_base62(8),
                    'entity_type' => $entityType,
                    'entity_id' => $entity->getKey()
                )
            );
        }

        // 返回 PlayID 对象
        return $playid;
    }


    /**
     * 使用 Model 实体对象删除 PlayID
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    static public function dropWithEntity($entity)
    {
        // 查找已存在的 PlayID
        $playid = self::isExistWithEntity($entity);

        if ($playid) {
            $playid->delete();
        }
    }


    /**
     * 获得实体类型的字符串描述
     * @param $object
     * @return bool|string
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    static protected function getEntityType($object)
    {
        $entityType = strtoupper(get_class($object));

        switch ($entityType) {
            case 'HIHO\MODEL\VIDEO':
                $entityType = 'VIDEO';
                break;
            case 'HIHO\MODEL\FRAGMENT':
                $entityType = 'FRAGMENT';
                break;
            case 'HIHO\MODEL\PLAYLIST':
                $entityType = 'PLAYLIST';
                break;
            case 'PLAYLIST':
                $entityType = 'PLAYLIST';
                break;
            default:
                return false;
        }
        return $entityType;
    }

    /**
     * 使用实体对象判断是否存在 PlayID
     * @param $entity
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    static public function isExistWithEntity($entity)
    {
        // 判断实体类型
        $entityType = self::getEntityType($entity);
        $entityId = $entity->getKey();

        if ($entityType and $entityId) {
            return self::isExistWithTypeAndId($entityType, $entityId);
        } else {
            return false;
        }
    }

    /**
     * 使用实体字符串判断是否存在 PlayId
     * @param $entityType
     * @param $entityId
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    static public function isExistWithTypeAndId($entityType, $entityId)
    {
        $playid = NULL;
        switch ($entityType) {
            case 'VIDEO':
                try {
                    $playid = self::where('entity_type', '=', 'VIDEO')
                        ->where('entity_id', '=', $entityId)->firstOrFail();
                } catch (ModelNotFoundException $e) {
                    return false;
                }
                break;
            case 'FRAGMENT':
                try {
                    $playid = self::where('entity_type', '=', 'FRAGMENT')
                        ->where('entity_id', '=', $entityId)->firstOrFail();
                } catch (ModelNotFoundException $e) {
                    return false;
                }
                break;
            case 'NOTE':
                try {
                    $playid = self::where('entity_type', '=', 'NOTE')
                        ->where('entity_id', '=', $entityId)->firstOrFail();
                } catch (ModelNotFoundException $e) {
                    return false;
                }
                break;
            case 'PLAYLIST':
                try {
                    $playid = self::where('entity_type', '=', 'PLAYLIST')
                        ->where('entity_id', '=', $entityId)->firstOrFail();
                } catch (ModelNotFoundException $e) {
                    return false;
                }
                break;
            default:
                return false;
        }

        return $playid;

    }

    /**
     * 判断是否存在某 PlayId
     * @param string $playid
     * @return bool
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    static public function isExistWithId($playid)
    {
        $playidObj = self::find($playid);

        if ($playidObj) {
            return $playidObj;
        } else {
            return false;
        }
    }

    /**
     * 生成一个 $l 位的 62 进制随机字符串, 其第一位字符不为数字
     * @param $l
     * @return string
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    static public function mt_rand_base62($l)
    {
        $c = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($s = '', $cl = strlen($c) - 1, $i = 0; $i < $l; $s .= $c[mt_rand(0, $cl)], ++$i) ;
        $s[0] = is_numeric($s[0]) ? $letters[mt_rand(0, strlen($letters) - 1)] : $s[0];
        return $s;
    }
}