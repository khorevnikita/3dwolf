<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\Order\OrderRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        list($page, $skip, $take) = Paginator::get($request);
        $models = Order::query();
        if ($request->has('search')) {
            $search = $request->get('search');
            $models = $models->whereHas("customer", function ($q) use ($search) {
                $q->search($search);
            });
        }
        $totalCount = $models->count();

        if ($take >= 0) {
            $models = $models->skip($skip)->take($take);
        }

        list($sort, $sortDir) = Paginator::getSorting($request);
        $models = $models->orderBy($sort, $sortDir);

        $models = $models->with('customer')->get();
        $pagesCount = Paginator::pagesCount($take, $totalCount);
        return $this->resourceListResponse('orders', $models, $totalCount, $pagesCount);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param OrderRequest $request
     * @return JsonResponse
     */
    public function store(OrderRequest $request): JsonResponse
    {
        $order = new Order($request->all());
        $order->save();

        $order->load('customer');
        return $this->resourceItemResponse('order', $order);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * @param OrderRequest $request
     * @param Order $order
     * @return JsonResponse
     */
    public function update(OrderRequest $request, Order $order): JsonResponse
    {
        $order->fill($request->all());
        $order->save();

        $order->load('customer');
        return $this->resourceItemResponse('order', $order);
    }

    /**
     * @param Order $order
     * @return JsonResponse
     */
    public function destroy(Order $order): JsonResponse
    {
        $order->delete();
        return $this->emptySuccessResponse();
    }
}
