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

    public function updateBalance(float $expense = 0, $expenseExpected = false, $income = 0, $incomeExpected = false)
    {
        var_dump($expense, $expenseExpected, $income, $incomeExpected);
        if ($expenseExpected) {
            $this->expected_income = $this->expected_income - $expense;
        } else {
            $this->balance = $this->balance - $expense;
        }

        if ($incomeExpected) {
            $this->expected_income = $this->expected_income + $income;
        } else {
            $this->balance = $this->balance + $income;
        }

        $this->save();
    }
}
