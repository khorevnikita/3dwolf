<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'customer_id', 'phone', 'amount', 'deadline', 'status', 'payment_status', 'delivery_address'];

    const STATUSES = ['new', 'printing', 'shipping', 'completed'];

    protected static function booted(): void
    {
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
