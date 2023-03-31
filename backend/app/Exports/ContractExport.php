<?php

namespace App\Exports;

use App\Models\Contract;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ContractExport implements FromView
{
    public Contract $contract;

    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('exports.contract', [
            'contract' => $this->contract,
            'date'=>$this->contract->getDate(),
            "template" => Contract::TEMPLATE_BLOCKS,
        ]);
    }
}
