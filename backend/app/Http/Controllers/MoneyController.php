<?php

namespace App\Http\Controllers;

use App\Http\Requests\Money\TotalStatistics;
use App\Models\Account;
use App\Models\Customer;
use App\Models\DeliveryAddress;
use App\Models\Order;
use App\Models\Part;
use App\Models\Payment;
use App\Models\PaymentPurpose;
use App\Models\RegularPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MoneyController extends Controller
{

    public function getDeliveryMethodsStats(Request $request): JsonResponse
    {
        $request->validate([
            'month' => "required|integer|min:0|max:12",
            'year' => "required|integer|min:0|max:3000",
        ]);

        $from = Carbon::create($request->get("year"), $request->get("month") + 1)->startOfMonth();
        $to = Carbon::create($request->get("year"), $request->get("month") + 1)->endOfMonth();

        $deliveryAddresses = DeliveryAddress::query() #->withSum("orders", "amount")
        ->with("orders", function ($q) use ($from, $to) {
            $q->where("orders.created_at", ">=", $from)
                ->where("orders.created_at", "<", $to);
        })
            ->get()->map(function (DeliveryAddress $address) {
                return [
                    "name" => $address->name,
                    "amount" => $address->orders->sum("amount"),
                    "count" => $address->orders->count()
                ];
            })->sortByDesc("amount")->values();

        return $this->resourceItemResponse('deliveryAddresses', $deliveryAddresses, [
            "from" => $from,
            "to" => $to,
        ]);
    }

    public function getPurposeStatistics(Request $request): JsonResponse
    {
        $request->validate([
            'month' => "required|integer|min:0|max:12",
            'year' => "required|integer|min:0|max:3000",
        ]);

        $from = Carbon::create($request->get("year"), $request->get("month") + 1)->startOfMonth();
        $to = Carbon::create($request->get("year"), $request->get("month") + 1)->endOfMonth();

        $purposes = PaymentPurpose::query() #->withSum('payments', 'amount')
        ->with("payments", function ($q) use ($from, $to) {
            $q->where("type", Payment::TYPES["EXPENSE"])
                ->where("created_at", ">=", $from)
                ->where("created_at", "<", $to);
        })->get()->map(function (PaymentPurpose $purpose) {
            return [
                "name" => $purpose->name,
                "color" => $purpose->color,
                "amount" => $purpose->payments->sum("amount"),
                "count" => $purpose->payments->count(),
            ];
        })->sortByDesc("amount")->values();

        return $this->resourceItemResponse("paymentPurposes", $purposes, [
            "from" => $from,
            "to" => $to,
        ]);
    }

    public function getTotalStatistics(TotalStatistics $request): JsonResponse
    {
        $year = (int)$request->get("year");
        $incomePayments = Payment::query()
            ->income()
            ->forYear((int)$request->get("year"))
            ->get();
        $totalIncome = $incomePayments->sum("amount");

        $expensePayments = Payment::query()
            ->expense()
            ->forYear((int)$request->get("year"))
            ->get();

        $totalExpense = $expensePayments->sum("amount");
        //$totalAccountSum = Account::query()->sum('balance');

        $balance = Account::query()->sum('balance');

        $fromDate = Carbon::create($year);
        $endDate = Carbon::create($year)->endOfYear();
        $monthData = $this->getMonthMoney($fromDate, $endDate, $incomePayments, $expensePayments);

        $accounts = Account::query()->get();

        $users = User::query()->moderator()->get();

        return $this->resourceItemResponse('data', [
            'income' => $totalIncome,
            'expense' => $totalExpense,
            'balance' => $balance,
            'months' => $monthData,
            'accounts' => $accounts,
            'users' => $users,
        ]);
    }

    public function getDashboardData(): JsonResponse
    {
        $user = auth("sanctum")->user();
        $fromDate = Carbon::now()->startOfYear();
        $endDate = Carbon::now()->endOfYear();
        $incomePayments = Payment::query()
            ->income()
            ->forYear(Carbon::now()->year)
            ->get();
        $expensePayments = Payment::query()
            ->expense()
            ->forYear(Carbon::now()->year)
            ->get();
        $monthData = $this->getMonthMoney($fromDate, $endDate, $incomePayments, $expensePayments);
        $accounts = Account::query()->get();

        $orders = Order::query()
            ->visible()
            ->selectRaw("status, count(id) as count")
            ->groupBy("status")->pluck('count', 'status')->toArray();

        $orders['completed_by_month'] = Order::query()
            ->visible()->whereStatus("completed")
            ->where("created_at", ">", Carbon::now()->startOfMonth())
            ->count();

        $sources = Customer::query()->selectRaw("source, count(id) as count")
            ->groupBy("source")->pluck('count', 'source');

        $stockData = $this->getStockData();
        $response = array_merge([
            'orders' => $orders,
        ], $user->isCustomer() ? [] : [
            'months' => $monthData,
            'accounts' => $accounts,
            'customers' => [
                'total' => Customer::query()->count(),
                'new' => Customer::query()->where("created_at", ">", Carbon::now()->startOfMonth())->count()
            ],
            'sources' => $sources,
            'stock' => $stockData,
            'client_credit_amount' => Customer::query()
                ->where("balance", ">", 0)
                ->sum("balance"),
            'parts_count' => Part::query()->where("status", "!=", "ended")->count(),
            'parts_weight' => Part::query()->where("status", "!=", "ended")->sum("weight"),
            'nearestPayments' => RegularPayment::getClosest(),
        ]);
        return $this->resourceItemResponse('data', $response);
    }

    protected function getMonthMoney($fromDate, $endDate, $incomePayments, $expensePayments): array
    {
        $pointer = Carbon::parse($fromDate);
        $monthData = [];

        while ($pointer <= $endDate) {
            $endPeriod = Carbon::parse($pointer)->endOfMonth()->format("Y-m-d H:i:s");
            $startPeriod = Carbon::parse($pointer)->startOfMonth()->format("Y-m-d H:i:s");

            $in = clone $incomePayments;
            $inPeriod = $in->where("paid_at", ">=", $startPeriod)
                ->where("paid_at", "<=", $endPeriod);
            $exp = clone $expensePayments;
            $expPeriod = $exp->where("paid_at", ">=", $startPeriod)
                ->where("paid_at", "<=", $endPeriod);
            $monthData[] = [
                'month' => $pointer->month,
                'start' => $startPeriod,
                'end' => $endPeriod,
                'in' => $inPeriod,
                'exp' => $expPeriod,
                'income' => $inPeriod->sum("amount"),
                'expense' => $expPeriod->sum("amount")
            ];
            $pointer->addMonth();
        }
        return $monthData;
    }

    protected function getStockData()
    {
        $sql = "select m.name as 'manufacturer',
       p1.prod_number,
       m2.name as 'material',
       p1.color,
       (select count(*)
        from parts p2
        where p2.manufacturer_id = p1.manufacturer_id
          and p2.prod_number = p1.prod_number
          and p2.material_id = p1.material_id
          and p2.color = p1.color
          and p2.status in ('new', 'opened')) count
from parts p1
         join manufacturers m on m.id = p1.manufacturer_id
         join materials m2 on m2.id = p1.material_id
group by p1.manufacturer_id, p1.prod_number, p1.material_id, p1.color
order by count;";
        return DB::select($sql);
    }
}
