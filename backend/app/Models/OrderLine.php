<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLine extends Model
{
    use HasFactory;

    protected $fillable = ['number', 'name', 'price', 'weight', 'print_duration', 'part_id', 'count'];

    protected $casts = [
        'price' => 'float',
        'total_weight' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function (OrderLine $model) {
            $model->setProperties();
        });

        static::created(function (OrderLine $model) {
            $model->calcOrderAmount();
        });

        static::updating(function (OrderLine $model) {
            $model->setProperties();
        });

        static::updated(function (OrderLine $model) {
            $model->calcOrderAmount();
        });

        static::deleted(function (OrderLine $model) {
            $model->calcOrderAmount();
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    public function setPrintDurationAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['print_duration'] = 0;
        } else {
            $this->attributes['print_duration'] = $value;
        }
    }

    public function setProperties()
    {
        $this->total_amount = round($this->price * $this->count, 2);
        $this->total_weight = round($this->weight * $this->count, 2);
    }

    public function calcOrderAmount()
    {
        $order = $this->order()->first();
        $amount = OrderLine::query()->where("order_id", $order->id)->sum('total_amount');
        $order->amount = $amount;
        $order->save();
    }

    public function copy(): OrderLine
    {
        $newLine = new OrderLine($this->toArray());
        $newLine->order_id = $this->order_id;
        $newLine->save();

        return $newLine;
    }
}
