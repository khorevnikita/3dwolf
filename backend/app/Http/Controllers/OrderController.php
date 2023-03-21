<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use App\Helpers\Paginator;
use App\Http\Requests\Order\OrderRequest;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

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
        if ($customerId = $request->get("customer_id")) {
            $models = $models->where("customer_id", $customerId);
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
     * @param Order $order
     * @return JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
        $order->load('customer');
        return $this->resourceItemResponse('order', $order);
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

    public function exportAuth(Order $order, Request $request)
    {
        auth('web')->login(auth('sanctum')->user());
        $hash = Hash::make("wolf-export-hash-$order->id");
        $type = $request->get('type');
        return $this->resourceItemResponse('download_link', url("api/orders/$order->id/export/$type?hash=$hash"));
    }

    public function exportXlsx(Order $order, Request $request)
    {
        if (!Hash::check("wolf-export-hash-$order->id", $request->get('hash'))) abort(403);
        $time = Carbon::now()->format("Y-m-d_H:i:s");
        $filename = "order_$order->id" . "_by_$time.xlsx";
        return Excel::download(new OrderExport($order), $filename);
    }

    public function exportPDF(Order $order, Request $request)
    {
        if (!Hash::check("wolf-export-hash-$order->id", $request->get('hash'))) abort(403);
        $time = Carbon::now()->format("Y-m-d_H:i:s");
        $filename = "order_$order->id" . "_by_$time.pdf";

        $pdf = Pdf::loadView('exports.order', [
            'order' => $order,
            'customer' => $order->customer,
        ])
            ->setPaper('a4', 'landscape');
        return $pdf->download($filename);
    }

    public function testExport(Order $order)
    {
        return view('exports.order', [
            'order' => $order,
            'customer' => $order->customer,
        ]);
    }
}
