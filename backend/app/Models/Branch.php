<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ["name", "responsible_person", "phone", "address", "working_hours", "is_default"];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
