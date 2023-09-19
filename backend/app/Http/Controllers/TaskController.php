<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
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
    public function schedule(Request $request): JsonResponse
    {
        list($page, $skip, $take) = Paginator::get($request);

        $tasks = Task::query()->selectRaw("DATE(datetime) as date, count(id) as total_count,SUM(case when completed = 1 then 1 else 0 end) as completed_count")
            ->groupBy("date")
            ->orderBy("date", "desc")
            ->skip($skip)
            ->take($take)
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

        $tasks = Task::query()->forDate($request->get("date"));
        if ($uid = $request->get("user_id")) {
            $tasks = $tasks->whereHas("users", function ($q) use ($uid) {
                $q->where("users.id", $uid);
            });
        }
        $tasks = $tasks->orderBy('datetime')->with("users")->get();

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

        if ($uids = $request->get("users_id")) {
            $task->users()->attach($uids);
        }

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
        if ($uids = $request->get("users_id")) {
            $task->users()->sync($uids);
        }
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

    public function notifyAll(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        Task::notifyForDay($request->get("date"));

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
