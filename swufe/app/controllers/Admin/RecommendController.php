<?php namespace HihoEdu\Controller\Admin;

use \View;
use \Recommend;
use HiHo\Model\VideoInfo;
use \Input;
use \Redirect;
use \Response;
use \RecommendPosition;
use \Teacher;

class RecommendController extends AdminBaseController{

    /**
     *新增推荐
     * @return mixed
     * @author zhuzhengqian
     */
    public function create(){
        $inputData = Input::all();
        if($inputData['positionId']){
            //check max num
            $position = $inputData['positionId'];
            $id = $inputData['check_id'];
            $type = $inputData['type'];
            $maxNum = RecommendPosition::find($position)->max_num;
            $recommendCount = Recommend::where('type',$type)->where('position_id',$position)->count();
            $objRecommend = new Recommend();
            $obj = Recommend::where('position_id',$position)->where('content_id',$id)->first();
            if($obj){
                $objRecommend = Recommend::find($obj->id);
            }else{
                if($recommendCount>=$maxNum){
                    return Response::json(array('msgCode'=>-2,'message'=>'over the max recommend num','data'=>NULL));
                }
            }
            $objRecommend->position_id = $position;
            $objRecommend->content_id = $id;
            $objRecommend->type = $type;
            $objRecommend->save();
            return Response::json(array('msgCode'=>0,'message'=>'recommend success','data'=>NULL));
        }else{
            //未选择推荐位
            return Response::json(array('msgCode'=>-1,'message'=>'must select recommend position','data'=>NULL));
        }
    }

    /**
     *取消推荐
     * @return mixed
     * @author zhuzhengqian
     */
    public function unRecommend($id){
        !$id and \App::abort(403);
        $objRecommend = Recommend::find($id);
        !$objRecommend and \App::abort(404);
        $objRecommend->delete();
        return \Redirect::to('/admin/position/recommend/'.$objRecommend->position_id.'/showRecommends')->with('success_tips','取消推荐成功');
    }

    /**
     *推荐讲师列表
     * @author zhuzhengqian
     */
    public function teacherIndex(){
        $teachers = \Teacher::paginate(25);
        if($teachers){
            foreach($teachers as $t){
                if($obj = Recommend::where('type','teacher')->where('content_id',$t->id)->first()){
                    $t->recommend_id = $obj->position_id;
                }
            }
        }
        $objRecommendTecher = RecommendPosition::where('type','teacher')->get();
        return View::make('admin.recommend.teacher_index',compact('teachers','objRecommendTecher'));
    }

    /**
     *推荐视频列表
     * @author zhuzhengqian
     */
    public function videoIndex(){
        $videos = \VideoInfo::paginate(30);
        if($videos){
            foreach($videos as $v){
                if($obj = Recommend::where('type','video')->where('content_id',$v->id)->first()){
                    $v->recommend_id = $obj->position_id;
                }
            }
        }
        $objRecommendVideo = RecommendPosition::where('type','video')->get();
        return View::make('admin.recommend.video_index',compact('videos','objRecommendVideo'));
    }
}

