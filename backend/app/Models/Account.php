<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'balance', 'expected_income'];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function updateBalance(float $expense = 0, float $income = 0)
    {
        $this->balance = $this->balance - $expense + $income;
        $this->save();
    }
}
