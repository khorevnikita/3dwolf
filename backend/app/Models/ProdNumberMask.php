<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdNumberMask extends Model
{
    use HasFactory;

    protected $fillable = ['prod_number', 'mask'];
}
