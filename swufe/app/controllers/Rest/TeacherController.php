<?php namespace HiHo\Edu\Controller\Rest;
/**
 * Created with JetBrains PhpStorm.
 * User: zhu
 * DateTime: 14-7-2 下午4:19
 * Email:www321www@126.com
 */
class TeacherController extends BaseController
{

    /**
     * 讲师列表
     * @return string
     */
    public function getIndex()
    {
        $inputData = \Input::only('department_id', 'page', 'limit', 'since_id');
        $sinceId = $inputData['since_id'] ? $inputData['since_id'] : 0;
        $limit = $inputData['limit'] ? $inputData['limit'] : parent::PAGE_SIZE;
        if ($inputData['department_id']) {
            $objTeacher = \DepartmentTeacher::where('department_id', $inputData['department_id'])->get();
            if ($objTeacher) {
                $arrIds = array();
                foreach ($objTeacher as $teacher) {
                    array_push($arrIds, $teacher->teacher_id);
                }
                $ids = implode(',', $arrIds);
            } else {
                $ids = '-1';
            }
            $objTeacher = \Teacher::whereRaw("id in ($ids) and id > $sinceId")->paginate($limit);
        } else {
            $objTeacher = \Teacher::whereRaw(" id > $sinceId")->paginate($limit);
        }

        return $this->encodeResult('12300', 'success', $objTeacher->toArray());
    }

    /**
     * 讲师下的课程索引
     * @param integer $teacher_id
     * @return string Json
     * @author zhengqian
     */
    public function videoBeyonds()
    {
        $inputData = \Input::only('teacher_id', 'page', 'limit', 'since_id');
        $rules = array(
            'teacher_id' => 'required'
        );
        $validataor = \Validator::make($inputData, $rules);
        //check params
        if ($validataor->fails()) {
            $errorMessage = $validataor->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $errorMessage->first()));
        }
        $sinceId = $inputData['since_id'] ? $inputData['since_id'] : 0;
        $limit = $inputData['limit'] ? $inputData['limit'] : parent::PAGE_SIZE;
        $videoList = \TeacherVideo::whereRaw("id > $sinceId and teacher_id = {$inputData['teacher_id']}")->paginate($limit);
        return $this->encodeResult('12301', 'success', $videoList->toArray());

    }

}
