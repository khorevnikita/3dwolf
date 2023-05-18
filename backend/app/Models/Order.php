<?php

namespace App\Models;

use App\Jobs\NotifyOrderChanged;
use App\Mail\OrderStatusChanged;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'customer_id', 'phone', 'amount', 'deadline', 'status', 'payment_status', 'delivery_address', 'comment'];

    const STATUSES = ['new', 'printing', 'shipping', 'completed', 'canceled'];

    protected static function booted(): void
    {
        static::updating(function (Order $model) {
            $amount = OrderLine::query()
                ->where("order_id", $model->id)
                ->sum('total_amount');
            $model->amount = $amount;
        });

        static::updated(function (Order $model) {
            if ($model->getOriginal('status') !== $model->status) {
                $customer = $model->customer;
                if ($model->status === "completed") {
                    $customer?->updateBalance($model->amount, 0);
                } elseif ($model->getOriginal('status') === "completed") {
                    $customer?->updateBalance(0, $model->amount);
                }
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function lines()
    {
        return $this->hasMany(OrderLine::class);
    }

    public function files()
    {
        return $this->hasMany(OrderFile::class);
    }

    public function copy(): Order
    {
        $newOrder = new Order($this->toArray());
        $newOrder->status = Order::STATUSES[0];
        $newOrder->payment_status = "not_paid";
        $newOrder->date = Carbon::now();
        $newOrder->deadline = Carbon::now();
        $newOrder->save();

        foreach ($this->lines()->get() as $line) {
            $line->copy();
            $line->order_id = $newOrder->id;
            $line->save();
        }

        foreach ($this->files()->get() as $file) {
            $file->copy();
            $file->order_id = $newOrder->id;
            $file->save();
        }

        return $newOrder;
    }

    /**
     * Отправить отбивку об изменении статуса
     *
     * @param string $channel email/sms
     * @return void
     */
    public function notify(string $channel, $attach = false): void
    {
        dispatch(new NotifyOrderChanged($this, $channel, $attach));
    }

    /**
     * Сохраняет актуальный PDF на диск
     * @return string
     */
    public function savePDF(): string
    {
        $fileName = "order_$this->id.pdf";
        $pdf = Pdf::loadView('exports.order', [
            'order' => $this,
            'customer' => $this->customer,
        ])
            ->setPaper('a4', 'landscape');
        $pdf->save($fileName, 'public');
        return $fileName;
    }
}
