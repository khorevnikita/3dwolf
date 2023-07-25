<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('randomize', function () {
    $customers = \App\Models\Customer::query()->get()->each(function (\App\Models\Customer $customer) {
        $faker = Faker\Factory::create('ru_RU');
        $name = $faker->firstName('male');
        $surname = $faker->lastName('male');
        $phone = $faker->phoneNumber();
        $email = $faker->safeEmail();
        $customer->fill([
            'name' => $name, 'surname' => $surname, 'phone' => $phone, 'email' => $email,
        ]);
        $customer->save();
        if ($user = $customer->user()->first()) {
            $user->fill([
                'name' => $name, 'surname' => $surname, 'phone' => $phone, 'email' => $email,
            ]);
            $user->save();
        }
        $customer->orders()->get()->each(function (\App\Models\Order $order) use ($phone) {
            $order->fill([
                'phone' => $phone,
            ]);
            $order->save();
        });
    });
})->purpose('Display an inspiring quote');
