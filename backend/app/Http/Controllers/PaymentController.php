<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\Payment\PaymentRequest;
use App\Models\Payment;
use Carbon\Carbon;
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

        if ($type = $request->get('type')) {
            $models = $models->whereType($type);
        }

        if ($search = $request->get('search')) {
            $models = $models->where("description", "like", "%$search%");
        }
        if ($userId = $request->get('user_id')) {
            $models = $models->whereUserId($userId);
        }
        if ($accountId = $request->get('account_id')) {
            $models = $models->whereAccountId($accountId);
        }

        if ($purposeId = $request->get("payment_purpose_id")) {
            $models = $models->wherePaymentPurposeId($purposeId);
        }

        if ($dates = array_filter(explode(",", $request->get("date")))) {
            if ((count($dates) === 2)) $models->where("paid_at", ">=", $dates[0])
                ->where("paid_at", "<=", $dates[1]);

            if (count($dates) === 1) $models->where("paid_at", ">=", Carbon::parse($dates[0])->startOfDay())
                ->where("paid_at", "<=", Carbon::parse($dates[0])->endOfDay());
        }


        $totalCount = $models->count();
        $totalSum = round($models->sum("amount"), 2);

        $models = $models->orderByRaw("CASE WHEN paid_at is null THEN 1 ELSE 0 END DESC")
            ->orderBy('paid_at', "desc")
            ->orderBy("id", "desc");
        if ($take >= 0) {
            $models = $models->skip($skip)->take($take);
        }
        $models = $models->with(['account', 'user', 'sourceAccount', 'purpose'])->get();
        $pagesCount = Paginator::pagesCount($take, $totalCount);
        return $this->resourceListResponse('payments', $models, $totalCount, $pagesCount, [
            'totalSum' => $totalSum,
        ]);
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
