<?php

namespace App\Http\Controllers;

use App\Models\OrderNotificationTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderNotificationTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $templates = OrderNotificationTemplate::query()->get();

        return $this->resourceListResponse('templates', $templates, $templates->count(), 1);
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
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'order_status' => 'required|unique:order_notification_templates,order_status',
            'template_email' => 'required',
            'template_sms' => 'required'
        ]);

        $model = new OrderNotificationTemplate($request->all());
        $model->save();

        return $this->resourceItemResponse('template', $model);
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderNotificationTemplate $orderNotificationTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderNotificationTemplate $orderNotificationTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrderNotificationTemplate $orderNotificationTemplate): JsonResponse
    {
        $request->validate([
            'order_status' => ["required", Rule::unique('order_notification_templates', 'order_status')->ignore($orderNotificationTemplate->id)],
            'template_email' => 'required',
            'template_sms' => 'required'
        ]);

        $orderNotificationTemplate->fill($request->all());
        $orderNotificationTemplate->save();

        return $this->resourceItemResponse('template', $orderNotificationTemplate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderNotificationTemplate $orderNotificationTemplate): JsonResponse
    {
        $orderNotificationTemplate->delete();
        return $this->emptySuccessResponse();
    }
}
