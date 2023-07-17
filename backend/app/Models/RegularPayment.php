<?php

namespace App\Models;

use Carbon\Carbon;
use Cron\CronExpression;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegularPayment extends Model
{
    use HasFactory;

    protected $fillable = ['schedule', 'recipient', 'user_id', 'description', 'amount'];

    protected $appends = ['next_date'];

    const CLOSEST_PERIOD = 3;

    /*protected $casts = [
        'next_date' => 'datetime'
    ];*/

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($q, string $needle)
    {
        if (!$needle) return $q;
        return $q->where(function ($q) use ($needle) {
            $q->where("recipient", "like", "%$needle%")
                ->orWhere("description", "like", "%$needle%")
                ->orWhereHas("user", function ($q) use ($needle) {
                    $q->search($needle);
                });
        });
    }

    public static function getClosest()
    {
        $allModels = self::all()->filter(function (RegularPayment $payment) {
            return Carbon::parse($payment->next_date)->diffInDays(now()) < RegularPayment::CLOSEST_PERIOD;
        })->sortBy("next_date");
        return $allModels->values();
    }

    public function getNextDateAttribute(): null|string
    {
        if (!$this->schedule) return null;
        $cron = new CronExpression($this->schedule);
        return $cron->getNextRunDate()->format("Y-m-d");
    }
}
