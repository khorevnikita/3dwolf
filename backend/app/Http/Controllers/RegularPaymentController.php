<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\RegularPayment\RegularPaymentRequest;
use App\Models\RegularPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegularPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        list($page, $skip, $take) = Paginator::get($request);
        $regularPayments = RegularPayment::query();
        if ($search = $request->get("search")) {
            $regularPayments = $regularPayments->search($search);
        }

        if ($uid = $request->get("user_id")) {
            $regularPayments = $regularPayments->where("user_id", $uid);
        }

        $totalCount = $regularPayments->count();

        if ($take >= 0) {
            $regularPayments = $regularPayments->skip($skip)->take($take);
        }
        list($sort, $sortDir) = Paginator::getSorting($request);
        $regularPayments = $regularPayments->orderBy($sort, $sortDir);

        $pagesCount = Paginator::pagesCount($take, $totalCount);

        $totalSum = $regularPayments->sum("amount");

        $regularPayments = $regularPayments->with('user')->get();

        return $this->resourceListResponse('regularPayments', $regularPayments, $totalCount, $pagesCount, [
            'totalSum' => $totalSum
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
    public function store(RegularPaymentRequest $request): JsonResponse
    {
        $regularPayment = new RegularPayment($request->all());
        $regularPayment->save();

        return $this->resourceItemResponse('regularPayment', $regularPayment);
    }

    /**
     * Display the specified resource.
     */
    public function show(RegularPayment $regularPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RegularPayment $regularPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RegularPaymentRequest $request, RegularPayment $regularPayment): JsonResponse
    {
        $regularPayment->fill($request->all());
        $regularPayment->save();

        return $this->resourceItemResponse('regularPayment', $regularPayment);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegularPayment $regularPayment): JsonResponse
    {
        $regularPayment->delete();

        return $this->emptySuccessResponse();
    }
}
