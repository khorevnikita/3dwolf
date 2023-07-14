<?php

namespace App\Http\Controllers;

use App\Http\Requests\Money\TotalStatistics;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MoneyController extends Controller
{
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
