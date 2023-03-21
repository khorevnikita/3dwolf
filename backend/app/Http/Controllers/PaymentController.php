<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\Payment\PaymentRequest;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        list($page, $skip, $take) = Paginator::get($request);
        $models = Payment::query();
        if ($request->has('order_id')) {
            $order_id = $request->get('order_id');
            $models = $models->where("order_id", "=", $order_id);
        }

        if ($request->has("customer_id")) {
            $customer_id = $request->get('customer_id');
            $models = $models->whereHas("order", function ($q) use ($customer_id) {
                $q->where("customer_id", $customer_id);
            });
        }

        $totalCount = $models->count();

        $models = $models->orderByRaw("CASE WHEN paid_at is null THEN 1 ELSE 0 END DESC")
            ->orderBy('paid_at', "desc")
            ->orderBy("id", "desc");
        if ($take >= 0) {
            $models = $models->skip($skip)->take($take);
        }
        $models = $models->with(['account', 'user'])->get();
        $pagesCount = Paginator::pagesCount($take, $totalCount);
        return $this->resourceListResponse('payments', $models, $totalCount, $pagesCount);
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
    public function store(PaymentRequest $request): JsonResponse
    {
        $payment = new Payment($request->all());
        $payment->save();

        $payment->load('account');
        return $this->resourceItemResponse('payment', $payment);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * @param PaymentRequest $request
     * @param Payment $payment
     * @return JsonResponse
     */
    public function update(PaymentRequest $request, Payment $payment): JsonResponse
    {
        $payment->fill($request->all());
        $payment->save();

        $payment->load('account');
        return $this->resourceItemResponse('payment', $payment);
    }

    /**
     *
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment): JsonResponse
    {
        $payment->delete();
        return $this->emptySuccessResponse();
    }
}
