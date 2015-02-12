<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 视频 MODEL
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class Video extends \Eloquent
{

    protected $primaryKey = 'video_id';

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    /**
     * 绑定加载事件
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public static function boot()
    {
        parent::boot();

        // 创建 PlayID
        self::created(function ($video) {
            $playid = PlayID::createWithEntity($video);
        });

        // 删除 PlayID
        self::deleted(function ($video) {
            PlayID::dropWithEntity($video);
        });
    }

    /**
     * 获得 PlayId 字符串
     * @return bool|null
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getPlayIdStr()
    {
        return PlayID::createWithEntity($this)->play_id;
    }

    public function favorites()
    {
        return $this->belongsToMany('User', 'favorites', 'video_id', 'user_id');
    }

    /**
     * 多专业
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function specialities()
    {
        return $this->belongsToMany('Speciality', 'videos_specialities');
    }

    /**
     * 多分类
     * @return mixed
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function categories()
    {
        return $this->belongsToMany('Category', 'videos_categories');
    }

    public function tags()
    {
        return $this->belongsToMany('Tag', 'videos_tags', 'video_id', 'tag_id');
    }

    public function subtitles()
    {
        return $this->hasMany('Subtitle');
    }

    public function info()
    {
        return $this->hasMany('VideoInfo', 'video_id');
    }

    public function pictures()
    {
        return $this->hasMany('VideoPicture');
    }

    public function resource()
    {
        // TODO: RENAME resources
        return $this->hasMany('VideoResource');
    }

    public function content_rating()
    {
        return $this->hasMany('VideoContentRating');
    }

    /**
     *更新该分类下的视频访问等级
     * @param $accessLevel
     * @param $categoryId
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public static function  resetAccessLevelByCategory($accessLevel, $categoryId)
    {
        \DB::table('videos')
            ->whereExists(function ($query) use ($categoryId) {
                $query->select(\DB::raw(1))
                    ->from('videos_categories')
                    ->whereRaw('videos_categories.video_id = videos.video_id and videos_categories.category_id =' . $categoryId);
            })
            ->update(array(
                'access_level' => $accessLevel
            ));
    }

    /**
     *检查用户对于某视频的综合权限
     * @param $user
     * @return bool
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function checkAccess($user = null)
    {
        $roleMaxLevel = 0;
        if ($user != null) {
            $roles = $user->roles;
            if ($roles == null) {
                $roleMaxLevel = 0;
            } else {
                $roleMaxLevel = $roles->max('access_level');
            }
        }
        $maxLevel = $this->access_level;
        $categoriesMax = $this->categories->max('access_level');
        $categoriesMax = $categoriesMax ? $categoriesMax : 0;
        $maxLevel = $categoriesMax > $maxLevel ? $categoriesMax : $maxLevel;
        return $roleMaxLevel >= $maxLevel;
    }

    /**
     *检查用户对于某视频的权限
     * @param $user
     * @return bool
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function checkVideoAccess($user = null)
    {
        $roleMaxLevel = 0;
        if ($user != null) {
            $roles = $user->roles;
            if ($roles == null) {
                $roleMaxLevel = 0;
            } else {
                $roleMaxLevel = $roles->max('access_level');
            }
        }
        $maxLevel = $this->access_level;
        $maxLevel = $maxLevel ? $maxLevel : 0;
        return $roleMaxLevel >= $maxLevel;
    }

    /**
     *检查用户对于某视频分类的权限
     * @param $user
     * @return bool
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function checkCategoryAccess($user = null)
    {
        $roleMaxLevel = 0;
        if ($user != null) {
            $roles = $user->roles;
            if ($roles == null) {
                $roleMaxLevel = 0;
            } else {
                $roleMaxLevel = $roles->max('access_level');
            }
        }
        $maxLevel = $this->categories->max('access_level');
        $maxLevel = $maxLevel ? $maxLevel : 0;
        return $roleMaxLevel >= $maxLevel;
    }

    /**
     * 重写delete方法，保证数据完整性
     * @auth Zhengqian
     * @return mixed
     */
    public function delete()
    {
        //删除重点
        \Annotation::where('video_id', $this->video_id)->delete();
        //删除评论
        \Comment::where('video_id', $this->video_id)->delete();
        //favorite
        \Favorite::where('play_id', $this->getPlayIdStr())->delete();
        //fragment
        \Fragment::where('video_id', $this->video_id)->delete();
        //fragmentResource
        \FragmentResource::where('video_id', $this->video_id)->delete();
        //highlight
        \Highlight::where('video_id', $this->video_id)->delete();
        //playlist fragment
        \PlaylistFragment::where('video_id', $this->video_id)->delete();
        //question
        \Question::where('video_id', $this->video_id)->delete();
        //subtitle
        \Subtitle::where('video_id', $this->video_id)->delete();
        //subtitle fulltext
        \SubtitleFt::where('video_id', $this->video_id)->delete();
        //teacher video
        \TeacherVideo::where('video_id', $this->video_id)->delete();
        //topic video
        \TopicVideos::where('video_id', $this->video_id)->delete();
        //video attachment
        \VideoAttachment::where('video_id', $this->video_id)->delete();
        //video category
        \VideoCategory::where('video_id', $this->video_id)->delete();
        //video contentRatinh
        \VideoContentRating::where('video_id', $this->video_id)->delete();
        //video info
        \VideoInfo::where('video_id', $this->video_id)->delete();
        //video pic
        \VideoPicture::where('video_id', $this->video_id)->delete();
        //video reference
        \VideosReference::where('video_id', $this->video_id)->delete();
        //video resource
        \VideoResource::where('video_id', $this->video_id)->delete();
        //video tag
        \VideoTag::where('video_id', $this->video_id)->delete();
        //Recommend
        \Recommend::where('type', 'video')->where('content_id', $this->video_id)->delete();

        return parent::delete();
    }
}