<?php

namespace App\Models;

use App\Jobs\NotifyOrderChanged;
use App\Mail\OrderStatusChanged;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'customer_id', 'branch_id', 'phone', 'amount', 'deadline', 'status', 'payment_status', 'delivery_address', 'comment', 'tk_link'];

    const STATUSES = ['new', 'modeling', 'printing', 'processing', 'moving', 'moving_tk', 'shipping', 'completed', 'canceled'];

    protected static function booted(): void
    {
        static::created(function (Order $order) {
            $order->generateQR();
        });
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

    public function branch()
    {
        return $this->belongsTo(Branch::class);
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

    public function scopeVisible($q)
    {
        $user = auth("sanctum")->user();
        if (!$user) return $q->where("id", 0);
        if ($user->isCustomer()) return $q->where("customer_id", $user->customer_id);
        return $q;
    }

    public function getAmountAfterDiscountAttribute()
    {
        if (!$this->discount) return $this->amount;
        $amount = $this->amount;
        $amount = $amount * (1 - $this->discount / 100);
        return round($amount, 2);
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

    public function generateQR()
    {
        $tmpFile = "tmp_qr_$this->id.png";
        $url = url("/orders/$this->id");
        (new QRCode())->render($url, $tmpFile);
        $rand = Str::random(12);
        $qrPath = "qr/$rand.png";
        Storage::disk("s3")->put($qrPath, file_get_contents($tmpFile));
        $this->qr = Storage::disk('s3')->url($qrPath);
        $this->save();
        unlink($tmpFile);
    }
}
