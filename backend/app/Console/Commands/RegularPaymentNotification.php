<?php

namespace App\Console\Commands;

use App\Helpers\Mutator;
use App\Models\RegularPayment;
use App\Models\SMSRU;
use App\Models\User;
use Cron\CronExpression;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RegularPaymentNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:regular-payments:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $payments = RegularPayment::query()->whereNotNull('schedule')->get();

        foreach ($payments as $payment) {
            try {
                $cron = new CronExpression($payment->schedule);
                if (!$cron->isDue()) continue;
            } catch (\Exception $exception) {
                Log::info("CRON ERROR", [
                    'error' => $exception->getMessage(),
                    'payment' => $payment,
                ]);
                continue;
            }

            $phones = User::query()->whereNotNull("phone")->whereHas("permission", function ($q) {
                $q->where("regular_payments", true);
            })->pluck("phone");

            $msg = "Регулярный платеж $payment->recipient";
            $phones->each(function (string $phone) use ($msg) {
                $sms = new SMSRU(config("services.smsru.key"));
                $post = (object)[
                    'to' => Mutator::numberToDigits($phone),
                    'msg' => $msg,
                    'from' => config("app.name"),
                    'test' => config("app.env") !== "production",
                ];
                $sent = $sms->send_one($post);
                Log::info("SMS SENT", ['data' => $sent]);
            });
        }
    }
}
