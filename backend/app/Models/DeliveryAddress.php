<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'text'];

    public function scopeSearch($q, $needle)
    {
        return $q->where(function ($q) use ($needle) {
            $q->where("name", "LIKE", "%$needle%")
                ->orWhere("text", "LIKE", "%$needle%");
        });
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }
}
