<?php

namespace App\Models;

use App\Mail\TaskNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

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
        $user = $this->user;
        $name = $this->name;
        $time = Carbon::parse($this->datetime)->format("H:i");
        $text = "$name в $time";

        if ($user->telegram_channel_id) {
            Telegram::request("sendMessage", [
                "chat_id" => $user->telegram_channel_id,
                "text" => $text,
            ]);
        } else {
            Mail::to($user->email)->queue(new TaskNotification($text));
        }
    }
}
