<?php namespace HihoEdu\Controller\Admin;

use \User;
use HiHo\Model\UserKv;
use HiHo\Model\Role;
use HiHo\Model\UserRole;

class UserController extends AdminBaseController
{


    /**
     *按照提交查找用户
     * @param null
     * @author zhuzhengqian
     */
    public function find(){
        $users = '';
        $field = '0';
        $keyword = '';
        $role = '0';
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $input = \Input::only('role','field','keyword');
            $keyword = addslashes(trim($input['keyword']));
            //check
            if($input['field'] == '0'){
                return \Redirect::to('/admin/users/find')->with('error_tips','请选择要查询的字段');
            }

            $role = $input['role'];
            $field = $input['field'];
            $where = '';
            switch($role){
                case '0':
                    $where = '';
                    break;
                case '1':
                    $where = ' and is_admin=0';
                    break;
                case '2':
                    $where = ' and is_admin=1';
                    break;
            }

            $users = User::whereRaw(" `{$field}` like '%{$keyword}%' {$where} ")->paginate(20);
        }
        return \View::make('admin.user.find',compact('users','field','role','keyword'));
    }

    /**
     * 用户管理列表
     * @author Zhengqian
     * @return mixed
     */
    public function getIndex()
    {
        $input = \Input::only('is_admin');
        if ($input['is_admin']) {
            $users = \User::where('is_admin', '1')->paginate(25);
        } else {
            $users = \User::paginate(25);
        }

        return \View::make('admin.user.index')->with('users', $users);
    }

    /**
     * 登录用户
     * @param int user_id
     * @return mixed
     * @author zhuzhengqian
     */
    public function getSessionLogin($userId)
    {
        if (!$userId || (!$objUser = \User::find($userId))) {
            return \App::abort(403);
        }
        \Auth::login($objUser);
        \Session::put('user_id', \Auth::user()->user_id);
        \Session::put('email', \Auth::user()->email);
        return \Redirect::to('/');
    }

    /**
     * 解锁用户
     * @param $userId
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function unlockUser($userId){
        $userObj = \User::find($userId);
        if ($userObj) {
            $userObj->status = \User::STATUS_NORMAL;
            $userObj->save();
            $loginFailKey = UserKv::LOGIN_FAIL_TIMES_KEY . '_' . $userObj->email;
            \Cache::forget($loginFailKey);
            $loginFailKey = UserKv::LOGIN_FAIL_TIMES_KEY . '_' . $userObj->mobile;
            \Cache::forget($loginFailKey);
            UserKv::where('user_id', $userObj->user_id)->where('key', UserKv::LOGIN_FAIL_LOCKED_KEY)->delete();
            return \Redirect::to('/admin/users')->with('success_tips', "<strong>解锁成功！</strong> 已成功解锁了 ID 为 $userObj->user_id 的用户。");
        } else {
            return \Redirect::to('/admin/users')->with('error_tips', "<strong>解锁成功！</strong> 试图操作的是错误或不存在的用户。");
        }
    }

    /**
     * 用户添加(GET)
     * @author Zhengqian
     * @return mixed
     */
    public function getCreate()
    {
        $roles = Role::all();
        return \View::make('admin.user.create')->with('roles',$roles);

    }

    /**
     * 用户添加(POST)
     * @author Zhengqian
     * @return mixed
     */
    public function postCreate()
    {
        $subDir = date('Ymd');
        $targetDir = public_path() . '/upload/' . $subDir;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777);
        }
        $input = \Input::all();
        $rule = array(
            'email' => 'email|required',
            'nikename'=>'required',
            'password' => 'min:6|max:48|required',
            'password_confirm' => 'same:password|required',
            'is_admin' => 'numeric|required',
            'role' => 'numeric|required',
        );

        $validator = \Validator::make($input, $rule);
        if ($validator->fails()) {
            $message = $validator->messages();
            return \Redirect::to('/admin/users/create')->with('error_tips',$message->first());
        }
        $roleId = $input['role'];
        //check email exist
        if(\User::where('email',$input['email'])->first()){
            return \Redirect::to('/admin/users/create')->with('error_tips',"该邮箱已经存在");
        }
        $role = Role::find($roleId);
        empty($role) and \App::abort('404', 'role id is not found');
        // check email,如果这个账号是以前删除掉的，那么直接将他还原！！
        if ($objDeleted = \User::withTrashed()->where('email', $input['email'])->first()) {
            $objDeleted->deleted_at = NULL;
            $objDeleted->password = \Hash::make($input['password']);
            $objDeleted->save();
            return \Redirect::to('/admin/users')->with('success_tips', "创建成功！");
        }

        $fileData = \Input::file('portrait');

        if ($fileData) {
            $allowExt = array('png', 'jpeg', 'bmp', 'gif');
            //check extension
            $ext = $fileData->guessExtension();
            if (!in_array($ext, $allowExt)) {
                return \Redirect::to('/admin/users/create')->with('error_tips', 'file type is illegal');
            }
            $realName = $fileData->getClientOriginalName();
            $randomName = date('His') . rand(1, 100) . '.' . $ext;
            //move
            $fileData->move($targetDir, $randomName);
        }

        // 新建用户保存
        $user = new \User();
        if ($fileData) {
            $user->avatar = '/upload/' . $subDir . '/' . $randomName;
        }

        $user->guid = \Uuid::v4();
        $user->email = $input['email'];
        $user->nickname = $input['nikename'];
        $user->password = \Hash::make($input['password']);
        $user->last_time = date('Y-m-d H:i:s');
        $user->status = \User::STATUS_NORMAL;
        $user->is_admin = intval($input['is_admin']);
        $user->save();

        $userSaved = User::where('guid', $user->guid)->first();
        $userRole = new UserRole();
        $userRole->role_id = $roleId;
        $userRole->user_id = $userSaved->user_id;
        $userRole->save();

        return \Redirect::to('/admin/users')->with('success_tips', "<strong>创建成功！</strong> 已成功创建新用户。");
    }

    /**
     * 删除用户
     * @author Zhengqian
     * @param null $user_id
     * @return mixed
     */
    public function getDestroy($user_id = NULL)
    {
        if (empty($user_id)) {
            return \Redirect::to('/admin/users')->with('error_tips', "<strong>删除失败！</strong> 试图操作的是错误或不存在的用户。");
        }

        $userObj = \User::find($user_id);

        if ($userObj) {
            \User::find($user_id)->delete();
            return \Redirect::to('/admin/users')->with('success_tips', "<strong>删除成功！</strong> 已成功删除了 ID 为 $userObj->user_id 的用户。");
        } else {
            return \Redirect::to('/admin/users')->with('error_tips', "<strong>删除失败！</strong> 试图操作的是错误或不存在的用户。");
        }

    }

    /**
     * 修改用户
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param null $user_id
     * @return mixed
     */
    public function getModify($user_id = NULL)
    {
        empty($user_id) and \App::abort('404', 'user id is required');
        $objUser = \User::find($user_id);
        empty($objUser) and \App::abort('404', 'user id is not found');
        return \View::make('admin.user.modify')->with(compact('objUser'));
    }

    /**
     * 修改用户
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return mixed
     */
    public function postModify()
    {
        $input = \Input::only('is_admin', 'user_id');

        $user_id = $input['user_id'];
        $is_admin = $input['is_admin'];

        \User::where('user_id', '=', $user_id)
            ->update(array('is_admin' => $is_admin));

        return \Redirect::to('/admin/users')->with('success_tips', "<strong>修改成功！</strong> 已保存新的用户资料。");

    }

    /**
     *注销管理员
     * @return mixed
     * @author zhuzhengqian
     */
    public function adminLogout()
    {
        \Session::forget('is_admin');
        \Session::forget('objAdmin');
        \Auth::logout();
        return \Redirect::to('/login');
    }
}
