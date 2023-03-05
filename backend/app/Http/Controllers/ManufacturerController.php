<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\Manufacturer\ManufacturerRequest;
use App\Models\Manufacturer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        list($page, $skip, $take) = Paginator::get($request);
        $models = Manufacturer::query();
        if ($request->has('search')) {
            $search = $request->get('search');
            $models = $models->where("name", "like", "%$search%");
        }
        $totalCount = $models->count();

        $models = $models->orderBy('name');
        if ($take >= 0) {
            $models = $models->skip($skip)->take($take);
        }
        $models = $models->get();
        $pagesCount = Paginator::pagesCount($take, $totalCount);
        return $this->resourceListResponse('manufacturers', $models, $totalCount, $pagesCount);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param ManufacturerRequest $request
     * @return JsonResponse
     */
    public function store(ManufacturerRequest $request): JsonResponse
    {
        $model = new Manufacturer($request->all());
        $model->save();
        return $this->resourceItemResponse('manufacturer', $model);
    }

    /**
     * Display the specified resource.
     */
    public function show(Manufacturer $manufacturer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Manufacturer $manufacturer)
    {
        //
    }

    /**
     * @param ManufacturerRequest $request
     * @param Manufacturer $manufacturer
     * @return JsonResponse
     */
    public function update(ManufacturerRequest $request, Manufacturer $manufacturer):JsonResponse
    {
        $manufacturer->fill($request->all());
        $manufacturer->save();
        return $this->resourceItemResponse('manufacturer', $manufacturer);
    }

    /**
     * @param Manufacturer $manufacturer
     * @return JsonResponse
     */
    public function destroy(Manufacturer $manufacturer):JsonResponse
    {
        $manufacturer->delete();
        return $this->emptySuccessResponse();
    }
}
