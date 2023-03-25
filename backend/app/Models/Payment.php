<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'user_id', 'order_id', 'account_id', 'paid_at', 'amount', 'description'];

    const TYPES = [
        'INCOME' => "income",
        'EXPENSE' => 'expense',
    ];

    protected $casts = ['amount' => 'float'];

    protected static function booted(): void
    {
        static::created(function (Payment $model) {
            $dir = $model->type === Payment::TYPES["INCOME"] ? 1 : -1;
            $amountChange = $dir * $model->amount;

            if ($model->account_id) {
                $account = Account::query()->find($model->account_id);
                $account?->updateBalance(0, false, $amountChange, is_null($model->paid_at));
            }

            if ($model->user_id) {
                $user = User::query()->find($model->user_id);
                $user?->updateBalance(0, $amountChange);
            }

            if ($model->order_id) {
                $customer = $model->order?->customer;
                $customer->updateBalance(0, $amountChange);
            }
        });

        static::updated(function (Payment $model) {
            $dir = $model->type === Payment::TYPES["INCOME"] ? 1 : -1;
            $originalAmount = $dir * $model->getOriginal('amount');
            $originalPaidDate = $model->getOriginal('paid_at');
            $amountChange = $dir * $model->amount;

            if ($model->account_id) {
                $account = Account::query()->find($model->account_id);
                $account?->updateBalance($originalAmount, is_null($originalPaidDate), $amountChange, is_null($model->paid_at));
            }

            if ($model->user_id) {
                $user = User::query()->find($model->user_id);
                $user?->updateBalance($originalAmount, $amountChange);
            }

            if ($model->order_id) {
                $customer = $model->order?->customer;
                $customer->updateBalance($originalAmount, $amountChange);
            }
        });

        static::deleted(function (Payment $model) {
            $dir = $model->type === Payment::TYPES["INCOME"] ? 1 : -1;
            $amountChange = $dir * $model->amount;

            if ($model->account_id) {
                $account = Account::query()->find($model->account_id);
                $account?->updateBalance($amountChange, is_null($model->paid_at), 0, false);
            }

            if ($model->user_id) {
                $user = User::query()->find($model->user_id);
                $user?->updateBalance($amountChange, 0);
            }

            if ($model->order_id) {
                $customer = $model->order?->customer;
                $customer->updateBalance($amountChange, 0);
            }
        });

    }


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
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

    public function getExpense()
    {

        if ($this->type === Payment::TYPES['INCOME']) return 0;
        return $this->amount;
    }

    public function getIncome()
    {
        if ($this->type === Payment::TYPES['EXPENSE']) return 0;
        return $this->amount;
    }

}
