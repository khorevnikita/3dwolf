<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()
            ->where("is_admin",true)
            ->first();

        if(!$user){
            $user = new User([
                'name'=>'ADMIN',
                'surname'=>'3D WOLF',
                'email'=>'admin@3dwolf.ru',
                'password'=>Hash::make("123456"),
            ]);
            $user->access_level = User::ACCESS["ADMIN"];
            $user->save();
        }
    }
}
