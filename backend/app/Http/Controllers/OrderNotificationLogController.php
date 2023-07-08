<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderNotificationLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderNotificationLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Order $order): JsonResponse
    {
        $logs = OrderNotificationLog::query()
            ->where("order_id", $order->id)
            ->orderBy("created_at", "desc")
            ->with("user")
            ->get();

        return $this->resourceListResponse('orderNotificationLogs', $logs, $logs->count(), 1);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderNotificationLog $orderNotificationLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderNotificationLog $orderNotificationLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrderNotificationLog $orderNotificationLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderNotificationLog $orderNotificationLog)
    {
        //
    }
}
