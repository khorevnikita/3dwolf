<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\Part\PartRequest;
use App\Models\Part;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        list($page, $skip, $take) = Paginator::get($request);
        $models = Part::query();

        if ($request->has('search')) {
            // todo: localScope
            $search = $request->get('search');
            $models = $models->where(function ($q) use ($search) {
                $q->where("inv_number", "like", "%$search%")
                    ->orWhere("color", "like", "%$search%");
            });
        }

        if ($manufacturerId = $request->get("manufacturer_id")) {
            $models = $models->whereManufacturerId($manufacturerId);
        }

        if ($materialId = $request->get("material_id")) {
            $models = $models->whereMaterialId($materialId);
        }

        if ($status = $request->get("status")) {
            $models = $models->whereStatus($status);
        }

        $totalCount = $models->count();

        if ($take >= 0) {
            $models = $models->skip($skip)->take($take);
        }

        if ($field = $request->get("field")) {
            $models = $models->orderByRaw("FIELD(id,$field) DESC");
        }

        list($sort, $sortDir) = Paginator::getSorting($request);
        $models = $models->orderBy($sort, $sortDir);

        $models = $models->with(['manufacturer', 'material'])->get();
        $pagesCount = Paginator::pagesCount($take, $totalCount);
        return $this->resourceListResponse('parts', $models, $totalCount, $pagesCount);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param PartRequest $request
     * @return JsonResponse
     */
    public function store(PartRequest $request): JsonResponse
    {
        $part = new Part($request->all());
        $part->save();
        return $this->resourceItemResponse('part', $part);
    }

    /**
     * Display the specified resource.
     */
    public function show(Part $part)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Part $part)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PartRequest $request, Part $part): JsonResponse
    {
        $part->fill($request->all());
        $part->save();
        return $this->resourceItemResponse('part', $part);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Part $part): JsonResponse
    {
        $part->delete();
        return $this->emptySuccessResponse();
    }
}
