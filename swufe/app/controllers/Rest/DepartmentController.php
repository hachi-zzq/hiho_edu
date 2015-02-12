<?php namespace HiHo\Edu\Controller\Rest;
/**
 * Created with JetBrains PhpStorm.
 * User: zhu
 * DateTime: 14-7-2 下午4:19
 * Email:www321www@126.com
 */
class DepartmentController extends BaseController{

    /**院系列表
     * @return string
     */
    public function getIndex(){
        $inputData = \Input::only('parent');
        $parent = $inputData['parent'] ? $inputData['parent'] : 0;
        $objDepartment = \Department::whereRaw("parent = $parent") ->get();
        return $this->encodeResult('12400','success',$objDepartment->toArray());
    }


}
