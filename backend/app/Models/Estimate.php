<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'name'];

    public function lines(){
        return $this->hasMany(EstimateLine::class);
    }
}