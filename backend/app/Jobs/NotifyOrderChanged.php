<?php

namespace App\Jobs;

use App\Mail\OrderStatusChanged;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderNotificationTemplate;
use App\Models\SMSRU;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyOrderChanged implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;
    public OrderNotificationTemplate|null $template;
    public Customer|null $customer;
    public string $channel;
    public bool $attachPDF;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, string $channel, bool $attachPDF)
    {
        $this->order = $order;
        $this->template = OrderNotificationTemplate::query()
            ->where("order_status", $order->status)
            ->first();
        $this->channel = $channel;
        $this->attachPDF = $attachPDF;
        $this->customer = $order->customer()->first();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->customer) {
            Log::info("CANT SEND NOTIFICATION: NO CUSTOMER");
            return;
        }

        if (!$this->template) {
            Log::info("CANT NOTIFY: NO TEMPLATE");
            return;
        }

        if ($this->channel === "sms") {
            $this->notifySMS();
        } elseif ($this->channel === "email") {
            $this->notifyEmail();
        } else {
            Log::info("CANT NOTIFY: WRONG CHANNEL");
        }
    }

    protected function notifySMS()
    {
        $msg = $this->template->template_sms;
        if (!$msg) {
            Log::info("CANT NOTIFY: NO TEMPLATE TEXT FOR SMS");
            return;
        }

        $msg = $this->fillTags($msg);

        $sms = new SMSRU(config("services.smsru.key"));
        $post = (object)[
            'to' => $this->customer->phone,
            'msg' => $msg,
            'from' => config("app.name"),
            'test' => config("app.env") !== "production",
        ];
        $sent = $sms->send_one($post);
        Log::info("SMS SENT", ['data' => $sent]);
    }

    protected function notifyEmail()
    {
        $html = $this->template->template_email;
        if (!$html) {
            Log::info("CANT NOTIFY: NO TEMPLATE TEXT FOR EMAIL");
            return;
        }

        $html = $this->fillTags($html);
        $file = null;
        if ($this->attachPDF) {
            $file = $this->order->savePDF();
        }
        Mail::to($this->customer->email)->queue(new OrderStatusChanged($html, $file));
    }

    protected function fillTags(string $template): string
    {
        $customer = $this->customer;
        // do something
        //[fio] - Фамилия Имя Отчество
        //[email] - Почта
        //[phone] - Телефон
        //[status] - Статус наряд-заказа
        //[total] - Сумма заказа
        //[total50] - 50% от суммы заказа
        //[address] - Адрес доставки
        $statusNames = [
            'new' => "новый",
            'printing' => "в печати",
            'moving' => "перемещение на ПВЗ",
            'shipping' => "готов к отгрузке",
            'completed' => 'отгружен',
            'canceled' => 'отменён'
        ];
        $tagMap = [
            "[fio]" => implode(" ", [$customer->surname, $customer->name, $customer->father_name]),
            "[email]" => $customer->email,
            "[phone]" => $customer->phone,
            "[status]" => $statusNames[$this->order->status] ?? "",
            "[total]" => $this->order->amount,
            "[total50]" => round($this->order->amount / 2),
            "[address]" => $this->order->delivery_address,
        ];
        $text = $template;
        foreach ($tagMap as $tag => $value) {
            $text = str_replace($tag, $value, $text);
        }
        return $text;
    }
}
