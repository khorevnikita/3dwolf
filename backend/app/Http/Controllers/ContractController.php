<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\Contract\ContractRequest;
use App\Models\Contract;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use iio\libmergepdf\Merger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        list($page, $skip, $take) = Paginator::get($request);
        $models = Contract::query();
        if ($request->has('search')) {
            $search = $request->get('search');
            $models = $models->where("number", "like", "%$search%");
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
        return $this->resourceListResponse('contracts', $models, $totalCount, $pagesCount);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param ContractRequest $request
     * @return JsonResponse
     */
    public function store(ContractRequest $request): JsonResponse
    {
        $contract = new Contract($request->all());
        $contract->save();

        $contract->load('customer');
        return $this->resourceItemResponse('contract', $contract);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract)
    {
        //
    }

    /**
     * @param ContractRequest $request
     * @param Contract $contract
     * @return JsonResponse
     */
    public function update(ContractRequest $request, Contract $contract): JsonResponse
    {
        $contract->fill($request->all());
        $contract->save();

        $contract->load('customer');
        return $this->resourceItemResponse('contract', $contract);
    }

    /**
     * @param Contract $contract
     * @return JsonResponse
     */
    public function destroy(Contract $contract): JsonResponse
    {
        $contract->delete();
        return $this->emptySuccessResponse();
    }

    public function exportAuth(Contract $contract, Request $request): JsonResponse
    {
        auth('web')->login(auth('sanctum')->user());
        $hash = Hash::make("wolf-export-contract-$contract->id");
        $type = $request->get('type');
        return $this->resourceItemResponse('download_link', url("api/contracts/$contract->id/export/$type?hash=$hash"));
    }

    public function exportPDF(Contract $contract, Request $request): Response
    {
        if (!Hash::check("wolf-export-contract-$contract->id", $request->get('hash'))) abort(403);
        $time = Carbon::now()->format("Y-m-d_H:i:s");
        $mainName = "contract_$contract->id" . "_text_by_$time.pdf";

        $mainPart = Pdf::loadView('exports.contract', [
            'contract' => $contract,
            'date' => $contract->getDate(),
            "template" => Contract::TEMPLATE_BLOCKS,
            'customer' => $contract->customer,
        ])
            ->setPaper('a4', 'portrait')->output();

        Storage::disk('public')->put($mainName, $mainPart);

        $additionName = "contract_$contract->id" . "_addition_by_$time.pdf";
        $addition = Pdf::loadView('exports.contract_addition', [
            'contract' => $contract,
            'date' => $contract->getDate(),
            'customer' => $contract->customer,
        ])
            ->setPaper('a4', 'landscape')->output();


        Storage::disk('public')->put($additionName, $addition);

        $merger = new Merger();
        $merger->addFile("storage/$mainName");
        $merger->addFile("storage/$additionName");

        $finalName = "contract_$contract->id" . "_by_$time.pdf";
        $createdPdf = $merger->merge();

        Storage::disk('public')->put($finalName, $createdPdf);

        Storage::disk('public')->delete($mainName);
        Storage::disk('public')->delete($additionName);
        return new Response(Storage::disk('public')->get($finalName), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $finalName . '"',
        ]);
    }

    public function exportAdditionPDF(Contract $contract, Request $request): Response
    {
        if (!Hash::check("wolf-export-contract-$contract->id", $request->get('hash'))) abort(403);
        $time = Carbon::now()->format("Y-m-d_H:i:s");
        $filename = "contract_$contract->id" . "_by_$time.pdf";

        $pdf = Pdf::loadView('exports.contract_addition', [
            'contract' => $contract,
            'date' => $contract->getDate(),
            'customer' => $contract->customer,
        ])
            ->setPaper('a4', 'landscape');
        return $pdf->download($filename);
    }


    public function testExport(Contract $contract): View
    {
        return view('exports.contract_addition', [
            'contract' => $contract,
            'date' => $contract->getDate(),
            'customer' => $contract->customer,
        ]);
    }
}
