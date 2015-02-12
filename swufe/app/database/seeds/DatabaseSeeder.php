<?php

class DatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        ##测试数据
        echo "Initing new users... \n";
        $this->call('UserTableSeeder');
    }

}

class UserTableSeeder extends Seeder
{


    public function run()
    {
        ## truncate users_role table
        DB::table('users_role')->truncate();
        ## truncate roles table
        DB::table('roles')->truncate();
        ## truncate user table
        DB::table('users')->truncate();

        ## init user data
        $user = new \User();
        $user->email = 'admin@example.com';
        $user->password = Hash::make('123qwe');
        $user->guid = \Uuid::v4();
        $user->is_admin = 1;
        $user->save();

        ## init user data
        $user = new \User();
        $user->email = 'demo@example.com';
        $user->password = Hash::make('123qwe');
        $user->guid = \Uuid::v4();
        $user->is_admin = 0;
        $user->save();

        // init test users
        for ($x = 1; $x < 100; $x++) {
            $user = new \User();
            $user->email = 'test' . $x . '@example.com';
            $user->password = Hash::make('123qwe');
            $user->guid = \Uuid::v4();
            $user->is_admin = 0;
            $user->save();
        }
        echo "New Admin User admin@example.com created, password is 123qwe.\n";
        echo "New Demo User demo@example.com created, password is 123qwe.\n";


        // 新建角色保存
        $role = new Role();
        $role->id = 1;
        $role->name = "普通用户";
        $role->access_level = 1;
        $role->description = '普通用户';
        $role->save();

        $role = new Role();
        $role->id = 2;
        $role->name = "临时学生";
        $role->access_level = 2;
        $role->description = '临时学生';
        $role->save();

        $role = new Role();
        $role->id = 3;
        $role->name = "普通学生";
        $role->access_level = 3;
        $role->description = '普通学生';
        $role->save();

        $role = new Role();
        $role->id = 4;
        $role->name = "教师";
        $role->access_level = 4;
        $role->description = '教师';
        $role->save();

    }

}