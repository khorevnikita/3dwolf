<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use App\Helpers\Paginator;
use App\Http\Requests\Order\OrderRequest;
use App\Http\Requests\Order\SetDiscountRequest;
use App\Models\Branch;
use App\Models\Estimate;
use App\Models\EstimateLine;
use App\Models\Order;
use App\Models\OrderLine;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $models = Order::query()->visible();
        if ($request->get('search')) {
            $search = $request->get('search');
            $models = $models->where(function ($q) use ($search) {
                $q->where("id", "=", "$search")/*->orWhereHas("customer", function ($q) use ($search) {
                    $q->search($search);
                })*/
                ;
            });
        }

        if ($customerId = $request->get("customer_id")) {
            $models = $models->where("customer_id", $customerId);
        }

        if ($status = $request->get("status")) {
            $models = $models->whereStatus($status);
        }

        if ($paymentStatus = $request->get("payment_status")) {
            $models = $models->wherePaymentStatus($paymentStatus);
        }

        if ($bid = $request->get("branch_id")) {
            $models = $models->where("branch_id", $bid);
        }

        $totalCount = $models->count();

        if ($take >= 0) {
            $models = $models->skip($skip)->take($take);
        }

        if ($orderId = $request->get("sort_order_id")) {
            $models = $models->orderByRaw("FIELD(id,$orderId) DESC");
        }

        if ($request->has("status_sort")) {
            $statuses = implode(",", array_map(function ($statusKey) {
                return "'$statusKey'";
            }, Order::STATUSES));
            $models = $models->orderByRaw("FIELD(status,$statuses) ASC");
        }

        list($sort, $sortDir) = Paginator::getSorting($request);
        $models = $models->orderBy($sort, $sortDir);

        $models = $models->with(['customer', 'branch'])->get();
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
        if (!$request->get("branch_id")) {
            $defaultBranch = Branch::query()->where("is_default", true)->first();
            if ($defaultBranch) {
                $order->branch_id = $defaultBranch->id;
            }
        }

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
        $order->load(['customer' => function ($q) {
            $q->withCount(['orders', 'recentOrders'])->withSum('payments', 'amount')->withSum('recentPayments', 'amount')
                ->with("lastPaidPayment");
        }]);
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

    public function setDiscount(SetDiscountRequest $request, Order $order): JsonResponse
    {
        $order->discount = $request->get("discount");
        $order->save();

        $order->load('customer');
        return $this->resourceItemResponse('order', $order);
    }

    public function notify(Order $order, Request $request): JsonResponse
    {
        $request->validate([
            'channel' => 'required|in:email,sms'
        ]);

        $order->notify($request->get("channel"), (bool)$request->get("attach"));

        return $this->emptySuccessResponse();
    }

    /**
     * Copy existing order
     * @param Order $order
     * @return JsonResponse
     */
    public function copy(Order $order): JsonResponse
    {
        $newOrder = $order->copy();
        $newOrder->load('customer');
        return $this->resourceItemResponse('order', $newOrder);
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

        $settings = DB::table("settings")->first();
        $pdf = Pdf::loadView('exports.order', [
            'order' => $order,
            'customer' => $order->customer,
            'settings' => (array)$settings,
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

    public function qr(Order $order)
    {
        if ($order->qr) {
            return $this->resourceItemResponse('url', $order->qr);
        }
        $order->generateQR();
        return $this->resourceItemResponse('url', $order->qr);
    }

    public function fillFromEstimate(Order $order, Request $request)
    {
        $request->validate([
            'estimate_id' => "required|integer|exists:estimates,id"
        ]);
        $lines = EstimateLine::query()
            ->where("estimate_id", $request->get("estimate_id"))
            ->get();

        DB::transaction(function () use ($order, $lines) {
            $lines->each(function (EstimateLine $line) use ($order) {
                $orderLine = new OrderLine([
                    ...$line->only(['name', 'price', 'count', 'weight', 'print_duration', 'part_id', 'filling']),
                    'name' => "Печать модели $line->name",
                    'order_id' => $order->id,
                ]);
                $orderLine->save();
            });
        });
    }
}
