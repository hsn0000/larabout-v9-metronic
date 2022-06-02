<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UserGroup extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Create User Group
         */
        $user_group = json_encode([[
            'id' => 1,
            'group' => 'Inactive',
            'roles' => NULL
        ],[
            'id' => 2,
            'group' => 'Registered',
            'roles' => NULL
        ],[
            'id' => 3,
            'group' => 'Superadmin',
            'roles' => '{"view":"master,user-data,user,user-group,client-token","create":"master,user-data,user,user-group,client-token","update":"master,user-data,user,user-group,client-token","delete":"master,user-data,user,user-group,client-token"}'
        ]]);

        foreach(json_decode($user_group) as $val)
        {
            DB::table('user_group')->insertOrIgnore([
                'guid' => $val->id,
                'group' => $val->group,
                'roles' => $val->roles ?: NULL
            ]);
        }

        /**
         * Create User
         */
        $user_group = json_encode([[
            'name' => 'supersu',
            'email' => 'supersu@gmail.com',
            'password' => \Hash::make('asdasd'),
            'guid' => 3
        ]]);

        foreach(json_decode($user_group) as $val)
        {
            DB::table('users')->insertOrIgnore([
                'name' => $val->name,
                'email' => $val->email,
                'password' => $val->password,
                'guid' => $val->guid,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
