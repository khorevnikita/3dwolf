<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\OrderLineRequest;
use App\Models\Order;
use App\Models\OrderLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderLineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Order $order, Request $request): JsonResponse
    {
        $lines = OrderLine::query()
            ->where("order_id", $order->id)
            ->with(['part.material', 'part.manufacturer'])
            ->get();

        return $this->resourceListResponse('orderLines', $lines, $lines->count(), 1);
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
    public function store(Order $order, OrderLineRequest $request): JsonResponse
    {
        $orderLine = new OrderLine($request->all());
        $orderLine->order_id = $order->id;
        $orderLine->save();
        return $this->resourceItemResponse('orderLine', $orderLine);
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderLine $orderLine): JsonResponse
    {
        return $this->resourceItemResponse('orderLine', $orderLine);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderLine $orderLine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderLineRequest $request, Order $order, OrderLine $orderLine): JsonResponse
    {
        $orderLine->fill($request->all());
        $orderLine->save();
        return $this->resourceItemResponse('orderLine', $orderLine);
    }

    /**
     * Copy order line
     * @param Order $order
     * @param OrderLine $orderLine
     * @return JsonResponse
     */
    public function copy(Order $order, OrderLine $orderLine): JsonResponse
    {
        $newLine = $orderLine->copy();
        $newLine->load(['part', 'part.material', 'part.manufacturer']);

        return $this->resourceItemResponse('orderLine', $newLine);
    }

    /**
     * @param Order $order
     * @param OrderLine $orderLine
     * @return JsonResponse
     */
    public function destroy(Order $order, OrderLine $orderLine): JsonResponse
    {
        $orderLine->delete();
        return $this->emptySuccessResponse();
    }
}
