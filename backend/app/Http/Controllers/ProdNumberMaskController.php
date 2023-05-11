<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdNumberMask\ProdNumberMaskRequest;
use App\Models\ProdNumberMask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProdNumberMaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $models = ProdNumberMask::query();

        if ($prodNumber = $request->get("prod_number")) {
            $models = $models->where("prod_number", $prodNumber);
        }

        $models = $models->orderBy("id", "desc")->get();

        return $this->resourceListResponse('prodNumberMasks', $models, $models->count(), 1);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProdNumberMaskRequest $request): JsonResponse
    {
        $mask = new ProdNumberMask($request->all());
        $mask->save();

        return $this->resourceItemResponse('prodNumberMask', $mask);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProdNumberMask $prodNumberMask)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProdNumberMask $prodNumberMask)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProdNumberMaskRequest $request, ProdNumberMask $prodNumberMask): JsonResponse
    {
        $prodNumberMask->fill($request->all());
        $prodNumberMask->save();
        return $this->resourceItemResponse('prodNumberMask', $prodNumberMask);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProdNumberMask $prodNumberMask): JsonResponse
    {
        $prodNumberMask->delete();
        return $this->emptySuccessResponse();
    }
}
