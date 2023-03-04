<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\Estimate\EstimateRequest;
use App\Models\Estimate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EstimateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        list($page, $skip, $take) = Paginator::get($request);
        $models = Estimate::query();
        if ($request->has('search')) {
            $search = $request->get('search');
            $models = $models->where("name", "like", "%$search%");
        }
        $totalCount = $models->count();

        if ($take >= 0) {
            $models = $models->skip($skip)->take($take);
        }

        list($sort, $sortDir) = Paginator::getSorting($request);
        $models = $models->orderBy($sort, $sortDir);

        $models = $models->get();
        $pagesCount = Paginator::pagesCount($take, $totalCount);
        return $this->resourceListResponse('estimates', $models, $totalCount, $pagesCount);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param EstimateRequest $request
     * @return JsonResponse
     */
    public function store(EstimateRequest $request): JsonResponse
    {
        $estimate = new Estimate($request->all());
        $estimate->save();
        return $this->resourceItemResponse('estimate', $estimate);
    }

    /**
     * @param Estimate $estimate
     * @return JsonResponse
     */
    public function show(Estimate $estimate):JsonResponse
    {
        $estimate->load('lines');
        return $this->resourceItemResponse('estimate', $estimate);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Estimate $estimate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EstimateRequest $request, Estimate $estimate): JsonResponse
    {
        $estimate->fill($request->all());
        $estimate->save();
        return $this->resourceItemResponse('estimate', $estimate);
    }

    /**
     * @param Estimate $estimate
     * @return JsonResponse
     */
    public function destroy(Estimate $estimate):JsonResponse
    {
        $estimate->delete();
        return $this->emptySuccessResponse();
    }
}
