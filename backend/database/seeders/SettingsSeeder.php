<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = DB::table("settings")->first();
        if (!$settings) {
            DB::table("settings")
                ->insert([
                    "brand_name" => "3D WOLF",
                    "legal_name" => "ИП Ковыршин К.А.",
                    "legal_full_name" => "Индивидуальный предприниматель Ковыршин Кирилл Анатольевич",
                    "legal_statement" => "ОГРНИП",
                    "ogrn" => "310774602101373 от 21.01.2010 г.",
                    "inn" => "771674399316",
                    "city" => "Москва",
                    "address" => "",
                    "bank" => 'ТОЧКА ПАО БАНКА "ФК ОТКРЫТИЕ" г Москва.',
                    "rs" => "40802810108500012357",
                    "ks" => "30101810745374525104",
                    "bik" => "044525104",
                    "phone" => "+7 (499) 113-1423",
                    "email" => "co@3dwolf.ru",
                    "website" => "https://3dwolf.ru",
                    "notification_time" => "08:00",
                ]);
        }
    }
}
