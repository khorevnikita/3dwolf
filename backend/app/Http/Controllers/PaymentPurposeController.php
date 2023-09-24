<?php

namespace App\Http\Controllers;

use App\Models\PaymentPurpose;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentPurposeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $paymentPurposes = PaymentPurpose::query()->orderBy("name")->get();
        return $this->resourceListResponse('paymentPurposes', $paymentPurposes, $paymentPurposes->count(), 1);
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
            "name" => "required|max:255|unique:payment_purposes,name",
            "color" => "required"
        ]);

        $paymentPurpose = new PaymentPurpose($request->only(['name', 'color']));
        $paymentPurpose->save();

        return $this->resourceItemResponse('paymentPurpose', $paymentPurpose);
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentPurpose $paymentPurpose)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentPurpose $paymentPurpose)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentPurpose $paymentPurpose): JsonResponse
    {
        $request->validate([
            "name" => ["required", "max:255", Rule::unique("payment_purposes", "name")->ignore($paymentPurpose->id)],
            "color" => "required"
        ]);

        $paymentPurpose->fill($request->only(['name', 'color']));
        $paymentPurpose->save();

        return $this->resourceItemResponse("paymentPurpose", $paymentPurpose);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentPurpose $paymentPurpose): JsonResponse
    {
        $paymentPurpose->delete();

        return $this->emptySuccessResponse();
    }
}
