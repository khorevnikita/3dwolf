<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    const TYPES = [
        'INCOME' => "income",
        'EXPENSE' => 'expense',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeIncome($q)
    {
        return $q->where("type", self::TYPES['INCOME']);
    }

    public function scopeExpense($q)
    {
        return $q->where("type", self::TYPES['EXPENSE']);
    }

    public function scopeForYear($q, int $year)
    {
        return $q->where("paid_at", ">=", Carbon::create($year))
            ->where("paid_at", "<=", Carbon::create($year)->endOfYear());
    }

}
