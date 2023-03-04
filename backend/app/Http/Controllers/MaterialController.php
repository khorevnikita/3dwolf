<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\Material\MaterialRequest;
use App\Models\Material;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        list($page, $skip, $take) = Paginator::get($request);
        $materials = Material::query();
        if ($request->has('search')) {
            $search = $request->get('search');
            $materials = $materials->where("name", "like", "%$search%");
        }
        $totalCount = $materials->count();

        $materials = $materials->orderBy('name');
        if ($take >= 0) {
            $materials = $materials->skip($skip)->take($take);
        }
        $materials = $materials->get();
        $pagesCount = Paginator::pagesCount($take, $totalCount);
        return $this->resourceListResponse('materials', $materials, $totalCount, $pagesCount);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param MaterialRequest $request
     * @return JsonResponse
     */
    public function store(MaterialRequest $request):JsonResponse
    {
        $model = new Material($request->all());
        $model->save();
        return $this->resourceItemResponse('material',$model);
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MaterialRequest $request, Material $material)
    {
        $material->fill($request->all());
        $material->save();
        return $this->resourceItemResponse('material',$material);
    }

    /**
     * @param Material $material
     * @return JsonResponse
     */
    public function destroy(Material $material):JsonResponse
    {
        $material->delete();
        return $this->emptySuccessResponse();
    }
}
