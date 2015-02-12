<?php namespace HihoEdu\Controller\Admin;

use HihoEdu\Controller\Admin\AdminBaseController;
use \RecommendPosition;
use \Recommend;
use \HiHo\Other\Pinyin;
use \Input;
use \Advertisement;
use \AdPosition;

class PositionController extends AdminBaseController
{

    /**
     *推荐位列表
     * @return mixed
     * @author zhuzhengqian
     */
    public function recommendIndex()
    {
        $objPosition = RecommendPosition::all();
        if(count($objPosition)){
            foreach($objPosition as $p){
                $p->exist_num = Recommend::where('position_id',$p->id)->count();
            }
        }
        return \View::make('admin.position.recommend_index',compact('objPosition'));
    }

    /**
     *新增推荐位
     * @return mixed
     * @author zhuzhengqian
     */
    public function recommendCreate(){
        //post create
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
           $inputData = Input::all();
            $rule = array(
                'name'=>'required',
                'max_number'=>'integer|required',
                'type'=>'required'
            );

           $validator = \Validator::make($inputData, $rule);
           if($validator->fails()){
               return \Redirect::to('/admin/positions/create')->with('error_tips',$validator->messages()->first());
           }
            $objPosition = new RecommendPosition();
            $objPosition->name = $inputData['name'];
            if( ! $inputData['class']){
                $pinyin = new Pinyin();
                $inputData['class'] = $pinyin->output($inputData['name']);
            }
            $objPosition->class = $inputData['class'];
            $objPosition->max_num = $inputData['max_number'];
            $objPosition->type = $inputData['type'];
            $objPosition->save();
            return \Redirect::to('/admin/positions/recommend/index')->with('success_tips','推荐位添加成功');
        }
        return \View::make('admin.position.recommend_create');
    }

    /**
     *推荐位修改
     * @return mixed
     * @author zhuzhengqian
     */
    public function recommendModify($id=NULL){
        $id = $id ? $id : Input::get('id');
        empty($id) and \App::abort('403');
        $objPosition = RecommendPosition::find($id);
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $inputData = Input::all();
            $rules = array(
                'name'=>'required',
                'max_number'=>'required|integer',
                'type'=>'required'
            );
            $validator = \Validator::make($inputData, $rules);
            if($validator->fails()){
                return \Redirect::to('/admin/positions/recommend/modify/'.$id)->with('error_tips',$validator->messages()->first());
            }

            $objPosition->name = $inputData['name'];
            if( ! $inputData['class']){
                $pinyin = new Pinyin();
                $inputData['class'] = $pinyin->output($inputData['name']);
            }
            $objPosition->max_num = $inputData['max_number'];
            $objPosition->class = $inputData['class'];
            $objPosition->type = $inputData['type'];
            $objPosition->save();
            return \Redirect::to('/admin/positions/recommend/index')->with('success_tips','推荐位修改成功');
        }
        return \View::make('admin.position.recommend_modify',compact('objPosition'));
    }


    /**
     *删除推荐位
     * @param null $id
     * @return mixed
     * @author zhuhzhengqian
     */
    public function recommendDestroy($id=NULL){
        ! $id and \App::abort(403);
        $objRecommendPosition = RecommendPosition::find($id);
        ! $objRecommendPosition  and \App::abort(404);
         if($objRecommendPosition->system == 1){
             return \Redirect::to('/admin/positions/recommend/index')->with('error_tips','系统推荐位，禁止删除');
         }
         //删除推荐位
        $objRecommendPosition->delete();

        return \Redirect::to('/admin/positions/recommend/index')->with('success_tips','推荐位删除成功');
    }

    /**
     *查看该推荐位下的推荐内容
     * @param null $id
     * @author zhuzhengqian
     */
    public function showRecommends($id=NULL){
        !$id and \App::abort(403);
        $objRecommendList = Recommend::where('position_id',$id)->get();
        return \View::make('admin.position.recommend_detail',compact('objRecommendList'));
    }


    /**
     *广告位列表
     * @return mixed
     * @author zhuzhengqian
     */
    public function advertisementIndex(){
        $objAdPositionList = AdPosition::all();
        if($objAdPositionList){
            foreach($objAdPositionList as $adP){
                $adP->type = $this->advertisementType($adP->type);
                $adP->adCount = Advertisement::where('position_id',$adP->id)->count();
            }
        }

        return \View::make('admin.position.ad_index',compact('objAdPositionList'));
    }

    /**
     *新增广告位
     * @return mixed
     * @author zhuzhengqian
     */
    public function advertisementCreate(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $inputData = Input::all();
            $objAd = new AdPosition();
            $objAd->name = $inputData['name'];
            $objAd->type = $inputData['type'];
            $objAd->description = $inputData['description'];
            $objAd->save();
            return \Redirect::to('/admin/positions/advertisement/index')->with('success_tips','广告位添加成功');
        }
        return \View::make('admin.position.ad_create');
    }


    /**
     * #开启关闭推荐位
     * @param $id
     */
    public function advertisementStatus($id){
        $objAdPosition = AdPosition::find($id);
        !$objAdPosition and \App::abort(404);
        if($objAdPosition->status == 1){
            $objAdPosition->status = 0;
        }elseif($objAdPosition->status == 0){
            $objAdPosition->status = 1;
        }
        $objAdPosition->save();
        return \Redirect::to('/admin/positions/advertisement/index')->with('success_tips','操作成功');
    }

    /**
     *该广告位下的广告
     * @param $id
     * @return mixed
     * @auhtor zhuzhengqian
     */
    public function showAds($id){
        $objAdPosition = AdPosition::find($id);
        !$objAdPosition and \App::abort(404);
        $objAdList = Advertisement::where('position_id',$id)->get();
        if($objAdList){
            foreach($objAdList as $list){
                $list->type = $this->advertisementType($list->type);
            }
        }

        return \View::make('admin.position.ad_detail',compact('objAdList','id'));

    }

    /**
     *广告位修改
     * @param $id
     * @author zhuzhengqian
     */
    public function advertisementModify($id){
        ! $id and \App::abort(403);
        $objPosition = AdPosition::find($id);
        ! $objPosition and \App::abort(404);
        ##post modify
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $inputData = \Input::all();
            $rules = array(
                'name'=>'required',
                'description'=>''
            );
            $validator = \Validator::make($inputData,$rules);
            if($validator->fails()){
                return \Redirect::to('/admin/positions/modify/'.$id)->with('error_tips',$validator->messages()->first());
            }
            //validator pass
            $objPosition->name = $inputData['name'];
            $objPosition->description = $inputData['description'];
            $objPosition->save();
            return \Redirect::to('/admin/positions/advertisement/index')->with('success_tips','广告位修改成功');
        }
        $objPosition->type = $this->advertisementType($objPosition->type);
        return \View::make('admin.position.ad_modify',compact('objPosition'));
    }

    /**
     *广告位删除
     * @param $id
     * @author zhuzhengqian
     */
    public function advertisementDestroy($id){
        ! $id and \App::abort(403);
        $objPosition = AdPosition::find($id);
        ! $objPosition and \App::abort(404);
        if($objPosition->system == 1){
            return \Redirect::to('/admin/positions/advertisement/index')->with('error_tips','系统广告位，禁止删除');
        }
        $objPosition->delete();
        return \Redirect::to('/admin/positions/advertisement/index')->with('success_tips','广告位删除成功');
    }


}
