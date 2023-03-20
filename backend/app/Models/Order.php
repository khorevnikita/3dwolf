<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'customer_id', 'phone', 'amount', 'deadline', 'status', 'payment_status', 'delivery_address'];

    protected static function booted(): void
    {
        static::updated(function (Order $model) {
            if ($model->getOriginal('status') !== $model->status) {
                if ($model->status === "completed") {
                    $customer = $model->customer;
                    $customer?->updateBalance($model->amount, 0);
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
