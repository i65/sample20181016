<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();//这里为什么写这个？不懂
        $this->call(UsersTableSeeder::class);
        Model::reguard();//这里也不懂
    }
}
