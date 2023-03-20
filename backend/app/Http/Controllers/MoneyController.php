<?php

namespace App\Http\Controllers;

use App\Http\Requests\Money\TotalStatistics;
use App\Models\Account;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        $balance = $totalAccountSum = Account::query()->sum('balance');

        $fromDate = Carbon::create($year);
        $endDate = Carbon::create($year)->endOfYear();
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

        $accounts = Account::query()->get();

        $users = User::query()->get();

        return $this->resourceItemResponse('data', [
            'income' => $totalIncome,
            'expense' => $totalExpense,
            'balance' => $balance,
            'months' => $monthData,
            'accounts' => $accounts,
            'users' => $users
        ]);
    }
}
