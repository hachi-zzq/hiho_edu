<?php namespace HiHo\Edu\Controller\Rest;

use \Speciality;
use Symfony\Component\CssSelector\Node\Specificity;


class SpecialityController extends BaseController
{

    /**
     * #专业索引
     * @return mixed
     * @author zhuzhengqian
     */
    public function getIndex(){
        // 表单验证规则
        $input = \Input::only('parent_id');
        $rules = array(
            'parent_id' => array(),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        if($input['parent_id']){
            $objSpeciality = Speciality::where('parent',$input['parent_id'])->get();
        }else{
            $objSpeciality = Speciality::all();
        }
        return $this->encodeResult('12700','success',$objSpeciality->toArray());
    }

}