<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $users = factory(User::class)->times(50)->make();
        User::insert($users->makeVisible(['password', 'remember_token'])->toArray());

        //声明第一个用户，方便后面登录
        $user = User::find(1);
        $user->name = "zhanyuanwen";
        $user->email = "2120676359@qq.com";
        $user->password = bcrypt('password');
        $user->is_admin = true;

        $user->save();
    }
}
