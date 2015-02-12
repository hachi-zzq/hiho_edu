<?php namespace HihoEdu\Controller\Admin;

use \AdPosition;
use \Advertisement;
use HiHo\Other\AdvertisementCall;
use \Input;
/**
 * Created with JetBrains PhpStorm.
 * User: zhu
 * DateTime: 14-6-23 上午11:04
 * Email:www321www@126.com
 */

class AdvertisementController extends AdminBaseController{

    /**
     *新增广告
     * @return mixed
     * @author zhuzhengqian
     */
    public function create($id){
        $id = $id ? $id : Input::get('id');
        ! $id and \App::abort(403);
        $objPosition = AdPosition::find($id);
        ! $objPosition and \App::abort(404);
        $objPosition->readable_type = $this->advertisementType($objPosition->type);
        ##post create
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $inputData = Input::all();
            //validator
            $rule = array(
                'ad_name'=>'required',
                'ad_description'=>'required',
                'type'=>'required',
            );
            $type = $inputData['type'];
            $validator = \Validator::make($inputData,$rule);
            if($validator->fails()){
                return \Redirect::to('/admin/advertisement/'.$id.'/create')->with('error_tips',$validator->messages()->first());
            }
            //save file
            if($fileData = Input::file('ad_img')){
                $subDir = date('Ymd');
                $targetDir = public_path() . '/upload/' . $subDir;
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777);
                }
                $allowExt = array('png', 'jpeg', 'bmp', 'gif');
                //check extension
                $ext = $fileData->guessExtension();
                if (!in_array($ext, $allowExt)) {
                    return \Redirect::to('/admin/advertisement/'.$id.'/create')->with('error_tips', 'file type is illegal');
                }
                $realName = $fileData->getClientOriginalName();
                $randomName = date('His') . rand(1, 100) . '.' . $ext;
                //move
                $fileData->move($targetDir, $randomName);
            }
            //save in mysql
            $objAd = new Advertisement();
            $objAd->position_id = $id;
            $objAd->name = $inputData['ad_name'];
            $objAd->description = $inputData['ad_description'];
            $objAd->type = $type;
            $objAd->fromtime = 0;
            $objAd->totime = 0;

            if($type=='picture' || $type=='rotation'){
                $objAd->img_src = '/upload/'.$subDir.'/'.$randomName;
                $objAd->href = $inputData['ad_href'];
            }elseif($type=='text'){
                $objAd->text_name = $inputData['ad_text'];
                $objAd->href = $inputData['ad_href'];
            }elseif($type=='code'){
                $objAd->code = $inputData['ad_code'];
            }
            $objAd->status = $inputData['status'];
            $objAd->save();
            return \Redirect::to('/admin/positions/advertisement/'.$id.'/showAds')->with('success_tips','广告添加成功');
        }
        return \View::make('admin.advertisement.create',compact('objPosition'));
    }


    /**
     *删除广告
     * @param $id
     * @author zhuzhengqian
     */
    public function destroy($id){
        ! $id and \App::abort(403);
        $objAd = Advertisement::find($id);
        ! $objAd and \App::abort(404);
        $objAd->delete();
        return \Redirect::to('/admin/positions/advertisement/'.$objAd->position_id.'/showAds')->with('success_tips','广告删除成功');
    }

    /**
     *修改广告
     * @param $id
     * @author zhuzhengqian
     */
    public function modify($id){
        $id = $id ? $id : Input::get('id');
        ! $id and \App::abort(403);
        $objAd = Advertisement::find($id);
        ! $objAd and \App::abort(404);
        $objPosition  = AdPosition::find($objAd->position_id);
        ##post create
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $inputData = Input::all();
            //validator
            $rule = array(
                'ad_name'=>'required',
                'ad_description'=>'required',
                'type'=>'required',
            );
            $type = $inputData['type'];
            $validator = \Validator::make($inputData,$rule);
            if($validator->fails()){
                return \Redirect::to('/admin/advertisement/'.$id.'/create')->with('error_tips',$validator->messages()->first());
            }
            $objAd = Advertisement::find($id);
            //save file
            if($fileData = Input::file('ad_img')){
                $subDir = date('Ymd');
                $targetDir = public_path() . '/upload/' . $subDir;
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777);
                }
                $allowExt = array('png', 'jpeg', 'bmp', 'gif');
                //check extension
                $ext = $fileData->guessExtension();
                if (!in_array($ext, $allowExt)) {
                    return \Redirect::to('/admin/advertisement/'.$id.'/create')->with('error_tips', 'file type is illegal');
                }
                $realName = $fileData->getClientOriginalName();
                $randomName = date('His') . rand(1, 100) . '.' . $ext;
                //move
                $fileData->move($targetDir, $randomName);
                $objAd->img_src = '/upload/'.$subDir.'/'.$randomName;
            }
            //save in mysql

            $objAd->name = $inputData['ad_name'];
            $objAd->description = $inputData['ad_description'];
            $objAd->type = $type;

            if($type=='picture' || $type=='rotation'){

                $objAd->href = $inputData['ad_href'];
            }elseif($type=='text'){
                $objAd->text_name = $inputData['ad_text'];
                $objAd->href = $inputData['ad_href'];
            }elseif($type=='code'){
                $objAd->code = $inputData['ad_code'];
            }
            $objAd->status = $inputData['status'];
            $objAd->save();
            return \Redirect::to('/admin/positions/advertisement/'.$objAd->position_id.'/showAds')->with('success_tips','广告添加成功');
        }
        return \View::make('admin.advertisement.modify',compact('objAd','objPosition'));
    }


    /**
     * #修改广告状态
     * @param $id
     * @author zhuzhengqian
     * @return mixed
     */
    public function status($id){
        $objAd = Advertisement::find($id);
        !$objAd and \App::abort(404);
        if($objAd->status == 1){
            $objAd->status = 0;
        }elseif($objAd->status == 0){
            $objAd->status = 1;
        }
        $objAd->save();
        return \Redirect::to('/admin/positions/advertisement/'.$objAd->position_id.'/showAds')->with('success_tips','操作成功');
    }

    /**
     * #广告排序
     * @param $postion_id
     * @return mixed
     * @author zhuzhengqian
     */
    public function sort($postion_id){
        ! $postion_id and \App::abort(403);
        $inputData = Input::all();
        $rule = array(
            'check_id'=>'required',
            'sort'=>'required'
        );
        $validator = \Validator::make($inputData,$rule);
        if($validator->fails()){
            return \Redirect::to('/admin/positions/advertisement/'.$postion_id.'/showAds')->with('error_tips',$validator->messages()->first());
        }
        ##pass check
        $arrId = $inputData['check_id'];
        if($arrId){
            foreach($arrId as $id){
                $objAd = Advertisement::find($id);
                $objAd->sort = intval($inputData['sort'][$id]);
                $objAd->save();
            }
        }
        return \Redirect::to('/admin/positions/advertisement/'.$postion_id.'/showAds')->with('success_tips','操作成功');
    }


}