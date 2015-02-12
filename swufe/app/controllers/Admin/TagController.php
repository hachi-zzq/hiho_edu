<?php namespace HihoEdu\Controller\Admin;
/**
 * Created with JetBrains PhpStorm.
 * User: zhu
 * DateTime: 14-6-10 下午5:19
 * Email:www321www@126.com
 */

class TagController extends AdminBaseController
{

    /**
     *tag列表
     * @return mixed
     * @author zhuzhengqian
     */
    public function index()
    {
        $tags = \DB::table('tags')->orderBy('created_at', 'desc')->paginate(20);
        return \View::make('admin.tag.list', compact('tags'));
    }

    public function addShow(){
        return \View::make('admin.tag.create', compact('tags'));
    }


    /**
     *添加tag
     * @return string
     * @author zhuzhengqian
     */
    public function add()
    {
        $postData = \Input::only('tag_name');
        $rules = array(
            'tag_name' => 'required'
        );
        $validator = \Validator::make($postData, $rules);
        ## validator fail
        if ($validator->fails()) {
            $messages = $validator->messages();
           return \Redirect::to('/admin/tag/add')->with('error_tips',$messages->first());
        }

        ##check exist
        $tag = \Tag::where('name', '=', $postData['tag_name'])->first();
        if ($tag) {
            return \Redirect::to('/admin/tag/add')->with('error_tips','该名称已经存在');
        }

        $tag = new \Tag();
        $tag->name = $postData['tag_name'];
        $tag->save();
        return \Redirect::to('/admin/tags/index')->with('success_tips','添加成功');

    }

    /**
     *删除tag
     * @param null $id
     * @return mixed
     * @author zhuzhengqian
     */
    public function delete($id = NULL)
    {
        empty($id) and \App::abort('404', 'tag id is required');
        $objTag = \Tag::find($id);
        empty($objTag) and \App::abort('404', 'tag id is not found');
        ##delete
        $objTag->delete();
        ##redirect
        return \Redirect::to('/admin/tags/index')->with('success_tips', '删除成功');

    }

    /**
     *修改tag
     * @return mixed
     * @author zhuzhengqian
     */
    public function modify($id=NULL)
    {
        $id = \Input::get('id') ? \Input::get('id') : $id;
        $objTag = \Tag::find($id);
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $name = \Input::get('tag_name');
            ##check
            if (trim($name) === '') {
                return \Redirect::to('/admin/tag/modify/'.$id)->with('error_tips', '名称不能为空');
            }
            ##check exist
            if(count(\Tag::whereRaw("name = '{$name}' and id != {$id}")->get())){
                return \Redirect::to('/admin/tag/modify/'.$id)->with('error_tips', '名称已经存在');
            }
            $objTag->name = $name;
            $objTag->save();
            ##redirect
            return \Redirect::to('/admin/tags/index')->with('success_tips', '修改成功');
        }

        return \View::make('admin.tag.modify', compact('objTag'));
    }


}
