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
        // 创建超级管理员
        User::create([
            'name' => 'Everan',
            'email' => 'everan@aliyun.com',
            'phone' => '18376662410',
            'identify' => 1,
            'password' => bcrypt('199457'),
            'avatar' => 'https://lccdn.phphub.org/uploads/avatars/17854_1500883966.jpeg?imageView2/1/w/100/h/100'
        ]);
    }
}
