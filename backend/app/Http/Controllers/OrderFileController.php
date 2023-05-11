<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Order $order): JsonResponse
    {
        $files = $order->files()->orderBy("id", "desc")->get();

        return $this->resourceListResponse('orderFiles', $files, $files->count(), 1);
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
    public function store(Order $order, Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|url',
            'size' => 'required|integer',
            'mime_type' => 'required',
            'name' => 'required|max:255'
        ]);

        $file = new OrderFile($request->all());
        $file->order_id = $order->id;
        $file->save();

        return $this->resourceItemResponse('orderFile', $file);
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderFile $orderFile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderFile $orderFile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrderFile $orderFile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order, OrderFile $orderFile): JsonResponse
    {
        $orderFile->delete();
        return $this->emptySuccessResponse();
    }
}
