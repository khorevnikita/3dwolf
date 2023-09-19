<?php

namespace App\Console\Commands;

use App\Helpers\Mutator;
use App\Mail\TaskNotification;
use App\Models\RegularPayment;
use App\Models\SMSRU;
use App\Models\Telegram;
use App\Models\User;
use Cron\CronExpression;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

            $recipients = User::query()->whereHas("permission", function ($q) {
                $q->where("regular_payments", true);
            })->get();

            $msg = "Регулярный платеж $payment->recipient";
            $recipients->each(function (User $user) use ($msg) {
                if ($user->tg_channel_id) {
                    Telegram::request("sendMessage", [
                        'chat_id' => $user->tg_channel_id,
                        'text' => $msg,
                    ]);
                } elseif ($user->phone) {
                    $sms = new SMSRU(config("services.smsru.key"));
                    $post = (object)[
                        'to' => Mutator::numberToDigits($user->phone),
                        'msg' => $msg,
                        'from' => config("app.name"),
                        'test' => config("app.env") !== "production",
                    ];
                    $sent = $sms->send_one($post);
                    Log::info("SMS SENT", ['data' => $sent]);
                } else {
                    Mail::to($user->email)->queue(new TaskNotification($msg));
                }
            });
        }
    }
}
