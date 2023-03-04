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
        return $this->price * $this->count;
    }

    public function getTotalWeightAttribute()
    {
        return $this->weight * $this->count;
    }

}
