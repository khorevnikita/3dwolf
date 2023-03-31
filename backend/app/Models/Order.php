<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'customer_id', 'phone', 'amount', 'deadline', 'status', 'payment_status', 'delivery_address','comment'];

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
}
