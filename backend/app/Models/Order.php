<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'customer_id', 'phone', 'amount', 'deadline', 'status', 'payment_status', 'delivery_address'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
