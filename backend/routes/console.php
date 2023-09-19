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


Artisan::command('reset', function () {
    \App\Models\User::query()->update([
        'password' => \Illuminate\Support\Facades\Hash::make(123456)
    ]);
})->purpose('Display an inspiring quote');

Artisan::command('qrs', function () {
    \App\Models\Order::query()->whereNull("qr")->get()->each(function (\App\Models\Order $order) {
        $order->generateQR();
    });
})->purpose('Display an inspiring quote');

Artisan::command('tg:set-webhook', function () {
    // https://core.telegram.org/bots/api#setwebhook
    \App\Models\Telegram::request("setWebhook", [
        "url" => url("api/tg/callback"),
        "allowed_updates" => ["message"],
    ]);
})->purpose('Display an inspiring quote');
