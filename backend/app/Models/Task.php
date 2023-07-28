<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'datetime',
        'description', 'name',
        'completed', 'notified',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setDatetimeAttribute($v)
    {
        if ($v) {
            $this->attributes['datetime'] = Carbon::parse($v)->format("Y-m-d H:i:s");
        }
    }

    public function scopeForDate($q, string $needle)
    {
        $date = Carbon::parse($needle);
        if ($date->isValid()) {
            return $q
                ->where("datetime", ">=", Carbon::parse($date)->startOfDay())
                ->where("datetime", "<=", Carbon::parse($date)->endOfDay());
        }
        return $q->where("id", 0);
    }

    public function sendNotification()
    {

    }
}
