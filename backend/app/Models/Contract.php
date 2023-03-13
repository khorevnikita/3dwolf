<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable=['number','date','customer_id','status','amount'];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}