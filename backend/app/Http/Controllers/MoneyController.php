<?php

namespace App\Http\Controllers;

use App\Http\Requests\Money\TotalStatistics;
use App\Models\Account;
use App\Models\Payment;
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

        $balance = $totalIncome - $totalExpense;

        $fromDate = Carbon::create($year);
        $endDate = Carbon::create($year)->endOfYear();
        $pointer = Carbon::parse($fromDate);
        $monthData = [];

        while ($pointer <= $endDate) {
            $monthData[] = [
                'month' => $pointer->month,
                'income' => $incomePayments->where("paid_at", ">=", Carbon::parse($pointer)->startOfMonth())
                    ->where("paid_at", ">=", Carbon::parse($pointer)->endOfMonth())
                    ->sum("amount"),
                'expense' => $expensePayments->where("paid_at", ">=", Carbon::parse($pointer)->startOfMonth())
                    ->where("paid_at", ">=", Carbon::parse($pointer)->endOfMonth())
                    ->sum("amount")
            ];
            $pointer->addMonth();
        }

        $accounts = Account::query()->get();

        return $this->resourceItemResponse('data', [
            'income' => $totalIncome,
            'expense' => $totalExpense,
            'balance' => $balance,
            'months' => $monthData,
            'accounts' => $accounts,
        ]);
    }
}
