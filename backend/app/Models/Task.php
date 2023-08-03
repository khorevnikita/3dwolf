<?php

namespace App\Models;

use App\Mail\TaskNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Builder;


class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'datetime',
        'description', 'name',
        'completed', 'notified',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        $user = auth("sanctum")->user();
        static::addGlobalScope('visible', function (Builder $builder) use ($user) {
            if ($user->access_level !== User::ACCESS["ADMIN"]) {
                $builder->whereHas('users', function (Builder $q) use ($user) {
                    $q->where("users.id", "=", $user->id);
                });
            }
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
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
        $task = $this;
        $this->users()->get()->each(function (User $user) use ($task) {
            $name = $task->name;
            $time = Carbon::parse($task->datetime)->format("H:i");
            $text = "$name в $time";

            if ($user->tg_channel_id) {
                Telegram::request("sendMessage", [
                    "chat_id" => $user->tg_channel_id,
                    "text" => $text,
                ]);
            } else {
                Mail::to($user->email)->queue(new TaskNotification($text));
            }
        });
    }

    public static function notifyForDay(string $day)
    {
        $users = User::query()->whereHas("tasks", function ($q) use ($day) {
            $q->forDate($day);
        })->get();

        $users->each(function (User $user) use ($day) {
            $userTasks = $user->tasks()->forDate($day)->orderBy('datetime')->get();
            $text = '';
            foreach ($userTasks as $k => $task) {
                $time = Carbon::parse($task->datetime)->format("H:i");
                $name = $task->name;
                $i = $k + 1;
                $text .= "$i. $name в $time \n";
            }

            if ($user->tg_channel_id) {
                Telegram::request("sendMessage", [
                    'chat_id' => $user->tg_channel_id,
                    'text' => $text,
                ]);
            } else {
                Mail::to($user->email)->queue(new TaskNotification(str_replace("\n", "<br/>", $text)));
            }
            Task::query()->whereIn("id", $userTasks->pluck("id"))->update(['notified' => 1]);
        });
    }
}
