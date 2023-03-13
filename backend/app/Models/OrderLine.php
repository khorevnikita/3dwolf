<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLine extends Model
{
    use HasFactory;

    protected $fillable = ['number', 'name', 'price', 'weight', 'print_duration', 'part_id', 'count'];

    protected static function booted(): void
    {
        static::creating(function (OrderLine $model) {
            $model->setProperties();
        });
        static::updating(function (OrderLine $model) {
            $model->setProperties();
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

    public function setProperties()
    {
        $this->total_amount = $this->price * $this->count;
        $this->total_weight = $this->weight * $this->count;
    }
}
