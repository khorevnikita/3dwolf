<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\Contract\ContractRequest;
use App\Models\Contract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function destroy(Contract $contract):JsonResponse
    {
        $contract->delete();
        return $this->emptySuccessResponse();
    }
}
