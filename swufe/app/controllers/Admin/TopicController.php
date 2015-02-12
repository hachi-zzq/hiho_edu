<?php namespace HihoEdu\Controller\Admin;


use HiHo\Other\Pinyin;

class TopicController extends AdminBaseController
{

    /**
     *topic列表
     * @return mixed
     * @author zhuzhengqian
     */
    public function index()
    {
        $topics = \Topic::orderBy('created_at', 'desc')->paginate(20);
        return \View::make('admin.topic.index', compact('topics'));
    }

    /**
     *添加tag
     * @return string
     * @author zhuzhengqian
     */
    public function create()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $postData = \Input::only('topic_name');
            $rules = array(
                'topic_name' => 'required'
            );
            $validator = \Validator::make($postData, $rules);
            ## validator fail
            if ($validator->fails()) {
                $messages = $validator->messages();
                return \Redirect::to('/admin/topics/create')->with('error_tips',$messages->first());
            }

            ##check exist
            $tag = \Topic::where('name', '=', $postData['topic_name'])->first();
            if ($tag) {
                return \Redirect::to('/admin/topics/create')->with('error_tips','该名称已经存在');
            }

            $tag = new \Topic();
            $pinyin = new Pinyin();
            $permalink = $pinyin->output($postData['topic_name']);
            if(\Topic::where('permalink',$permalink)->first()){
                $permalink = $permalink.str_random(3);
            }
            $tag->name = $postData['topic_name'];
            $tag->permalink = $permalink;
            $tag->save();
            return \Redirect::to('/admin/topics/index')->with('success_tips','添加成功');
        }

        return \View::make('admin.topic.create');

    }

    /**
     *删除tag
     * @param null $id
     * @return mixed
     * @author zhuzhengqian
     */
    public function delete($id = NULL)
    {
        empty($id) and \App::abort('404', 'topic id is required');
        $objTag = \Topic::find($id);
        empty($objTag) and \App::abort('404', 'topic id is not found');
        ##delete
        $objTag->delete();
        ##redirect
        return \Redirect::to('/admin/topics/index')->with('success_tips', '删除成功');

    }

    /**
     *修改tag
     * @return mixed
     * @author zhuzhengqian
     */
    public function modify($id)
    {
        $id = \Input::get('id') ? \Input::get('id') : $id;
        $objTopic = \Topic::find($id);
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $name = \Input::get('topic_name');
            ##check
            if (trim($name) === '') {
                return \Redirect::to('/admin/topics/modify/'.$id)->with('error_tips', '名称不能为空');
            }
            ##check exist
            if($obj = \Topic::whereRaw("name = '{$name}' and id != {$id}")->first()){
                return \Redirect::to('/admin/topics/modify/'.$id)->with('error_tips', '名称已经存在');
            }
            $objTopic->name = $name;
            $objTopic->save();
            ##redirect
            return \Redirect::to('/admin/topics/index')->with('success_tips', '修改成功');
        }

        return \View::make('admin.topic.modify', compact('objTopic'));
    }


}
