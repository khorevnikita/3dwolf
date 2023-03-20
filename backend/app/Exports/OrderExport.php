<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;


class OrderExport implements FromView
{

    public Order $order;
    public Customer $customer;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->customer = $order->customer()->first();
    }

    public function view(): View
    {
        return view('exports.order', [
            'order' => $this->order,
            'customer'=>$this->customer,
        ]);
    }
}
