<?php
  namespace HiHo\Controller\Push;
  use HiHo\Model\TvShowLog;
  
  class TvInfoController extends BaseController
  {
  	 /**
  	  * @name 获取tvs信息并入库
  	  * @access public
  	  * @author AndyLee <guanjun.li@autotiming.com>
  	  */
  	 public function getData(){
  	 	 $post = \Input::all();
 
  	 	 if(empty($post)){
  	 	 	
  	 	 	return \Response::json(array('msg' => 'post error', 'state' => '-0'));

  	 	 }else{
  	 	 	$rules =array(
	  	 	 			 'tv_id' => array('required', 'integer'),
	  	 	 			 'start_time' => array('required', 'date'),
	  	 	 			 'finish_time' => array('required', 'date'),
	  	 	 			 'show_name' => array('required'),
  	 	 			     'pull_time' => array('required')
  	 	 	        );
  	 
  	 	 	$validator = \Validator::make($post,$rules);
  	 	 	
  	 	 	if ($validator->fails())
  	 	 	{
  	 	 		 return \Response::json(array('msg' => $validator->messages()->first(),'state' => '-1'));
  	 	 	}else{
  	 	 		
                 $log = new TvShowLog();
                 $log->tv_id = $post['tv_id'];
                 $log->start_time = $post['start_time'];
                 $log->end_time = $post['finish_time'];
                 $log->show_name = $post['show_name'];
                 $log->pulltime = $post['pull_time'];
                 $log->version = date("Ymd",time());
                 $log->save();
                 
                 return \Response::json(array('msg' => 'save successed', 'state' => '1'));
  	 	 	}
  	 	 	
  	 	 }
  	 }
  }