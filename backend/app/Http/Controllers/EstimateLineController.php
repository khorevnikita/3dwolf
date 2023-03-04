<?php

namespace App\Http\Controllers;

use App\Http\Requests\Estimate\EstimateLineRequest;
use App\Models\Estimate;
use App\Models\EstimateLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EstimateLineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Estimate $estimate): JsonResponse
    {
        $lines = EstimateLine::query()->where("estimate_id", $estimate->id)->get();
        return $this->resourceListResponse('estimateLines', $lines, $lines->count(), 1);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param EstimateLineRequest $request
     * @return JsonResponse
     */
    public function store(Estimate $estimate, EstimateLineRequest $request): JsonResponse
    {
        $line = new EstimateLine($request->all());
        $line->estimate_id = $estimate->id;
        $line->save();
        return $this->resourceItemResponse('estimateLine', $line);
    }

    /**
     * Display the specified resource.
     */
    public function show(EstimateLine $estimateLine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EstimateLine $estimateLine)
    {
        //
    }

    /**
     * @param EstimateLineRequest $request
     * @param EstimateLine $estimateLine
     * @return JsonResponse
     */
    public function update(EstimateLineRequest $request, Estimate $estimate, EstimateLine $estimateLine)
    {
        $estimateLine->fill($request->all());
        $estimateLine->save();
        return $this->resourceItemResponse('estimateLine', $estimateLine);
    }

    /**
     * @param EstimateLine $estimateLine
     * @return JsonResponse
     */
    public function destroy(Estimate $estimate,EstimateLine $estimateLine): JsonResponse
    {
        $estimateLine->delete();
        return $this->emptySuccessResponse();
    }
}
