<?php namespace HihoEdu\Controller\Admin;

use HiHo\Model\Role;
use HiHo\Model\UserRole;

class RoleController extends AdminBaseController
{
    /**
     * 角色
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function index()
    {
        $roles = Role::paginate(20);
        return \View::make('admin.role.index')->with('roles', $roles);
    }

    /**
     *跳转到新建页面
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function getCreate()
    {
        return \View::make('admin.role.create');

    }

    /**
     * 创建角色
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function postCreate()
    {
        $input = \Input::only('name', 'access_level', 'description');
        $rule = array(
            'name' => 'required',
            'access_level' => 'required|integer|min:1',
        );
        $validator = \Validator::make($input, $rule);
        if ($validator->fails()) {
            $message = $validator->messages();
            return \Redirect::to('/admin/roles/create')->with('error_tips', $message->first());
        }
        $countName = Role::where('name',$input['name'])->count();
        if ($countName > 0) {
            return \Redirect::to('/admin/roles/create')->with('error_tips', '<strong>创建失败！</strong> 角色名称不能重复。');
        }
        // 新建角色保存
        $role = new Role();
        $role->name = $input['name'];
        $role->access_level = $input['access_level'];
        $role->description = $input['description'];
        $role->save();
        return \Redirect::to('/admin/roles')->with('success_tips', "<strong>创建成功！</strong> 已成功创建新角色。");
    }

    /**
     *删除角色
     * @param null $role_id
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function getDestroy($role_id = NULL)
    {
        if (empty($role_id)) {
            return \Redirect::to('/admin/roles')->with('error_tips', "<strong>删除失败！</strong> 试图操作的角色错误或不存在的角色。");
        }
        $role = Role::find($role_id);
        if ($role) {
            if ($role->id < 5) {
                return \Redirect::to('/admin/roles')->with('error_tips', "<strong>删除失败！</strong> 试图操作的是系统级角色。");
            }
            $subUsersCount = UserRole::where('role_id', $role->id)->count();
            if ($subUsersCount > 0) {
                return \Redirect::to('/admin/roles')->with('error_tips', "<strong>删除失败！</strong> 试图操作的角色下含有用户。");
            }
            UserRole::where('role_id', $role->id)->delete();
            Role::find($role_id)->delete();
            return \Redirect::to('/admin/roles')->with('success_tips', "<strong>删除成功！</strong> 已成功删除了 ID 为 $role->id 的角色。");
        } else {
            return \Redirect::to('/admin/roles')->with('error_tips', "<strong>删除失败！</strong> 试图操作的角色错误或不存在的角色。");
        }

    }

    /**
     *跳转到修改角色页面
     * @param null $role_id
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function getModify($role_id = NULL)
    {
        empty($role_id) and \App::abort('404', 'role id is required');
        $role = Role::find($role_id);
        empty($role) and \App::abort('404', 'role id is not found');
        return \View::make('admin.role.modify')->with('role', $role);
    }

    /**
     * 修改角色
     * @param $role_id
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function postModify($role_id)
    {
        $input = \Input::only('name', 'access_level', 'description');
        $rule = array(
            'name' => 'required',
            'access_level' => 'required|integer|min:1',
        );
        $validator = \Validator::make($input, $rule);
        if ($validator->fails()) {
            $message = $validator->messages();
            return \Redirect::to('/admin/roles/modify/'.$role_id)->with('error_tips', $message->first());
        }
        // 新建角色保存
        $role = Role::where('id',$role_id)->first();
        empty($role) and \App::abort('404', 'role id is not found');
        $countName = Role::where('name', $input['name'])->where('id', '!=', $role_id)->count();
        if ($countName > 0) {
            return \Redirect::to('/admin/roles/modify/'.$role_id)->with('error_tips', '<strong>创建失败！</strong> 角色名称不能重复。');
        }
        $role->name = $input['name'];
        $role->access_level = $input['access_level'];
        $role->description = $input['description'];
        $role->save();
        return \Redirect::to('/admin/roles')->with('success_tips', "<strong>修改成功！</strong> 已保存新的角色信息。");
    }

}
