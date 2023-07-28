<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\TaskRequest;
use App\Mail\TaskNotification;
use App\Models\Task;
use App\Models\Telegram;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    public function schedule(): JsonResponse
    {
        $tasks = DB::table("tasks")
            ->selectRaw("DATE(datetime) as date, count(id) as count")
            ->groupBy("date")
            ->orderBy("date", "desc")
            ->get();

        return $this->resourceListResponse('tasksSchedule', $tasks, count($tasks), 1);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            "date" => 'required|date'
        ]);

        $tasks = Task::query()->forDate($request->get("date"))->orderBy('datetime')->with("user")->get();

        return $this->resourceListResponse('tasks', $tasks, count($tasks), 1);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request): JsonResponse
    {
        $task = new Task($request->all());
        $task->save();

        return $this->resourceItemResponse('task', $task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task): JsonResponse
    {
        $task->fill($request->all());
        $task->save();

        return $this->resourceItemResponse('task', $task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();
        return $this->emptySuccessResponse();
    }

    public function notifyAll(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $userTasks = Task::query()->forDate($request->get("date"))->with("user")->get()->groupBy("user_id");

        /*$tasks->each(function (Task $task) {
            $task->sendNotification();
        });*/

        $userTasks->each(function ($userTasks) {
            Log::info("notify", ['userTasks' => $userTasks]);
            $notificationText = $userTasks->map(function (Task $task) {
                return "$task->name в " . Carbon::parse($task->datetime)->format("H:i");
            })->implode("\n");
            $user = $userTasks->first()?->user;
            if ($user->telegram_channel_id) {
                Telegram::request("sendMessage", [
                    'chat_id' => $user->telegram_channel_id,
                    'text' => $notificationText,
                ]);
            } else {
                Mail::to($user->email)->queue(new TaskNotification($notificationText));
            }
            Task::query()->whereIn("id", $userTasks->pluck("id"))->update(['notified' => 1]);
        });


        return $this->emptySuccessResponse();
    }

    public function notify(Task $task): JsonResponse
    {
        $task->sendNotification();
        $task->notified = true;
        $task->save();
        return $this->emptySuccessResponse();
    }

    public function complete(Task $task, Request $request)
    {
        $task->completed = $request->get("completed") ? 1 : 0;
        $task->save();

        return $this->emptySuccessResponse();
    }
}
