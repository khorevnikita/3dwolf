<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateLine extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'name', 'price', 'count', 'weight', 'print_duration'];

    protected $appends = ['amount', 'total_weight'];

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    public function getAmountAttribute()
    {
        return round($this->price * $this->count, 2);
    }

    public function getTotalWeightAttribute()
    {
        return round($this->weight * $this->count, 2);
    }

}
