<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\Branch\BranchRequest;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        list($page, $skip, $take) = Paginator::get($request);
        $branches = Branch::query();
        if ($search = $request->get("search")) {
            $branches = $branches->search($search);
        }

        $totalCount = $branches->count();

        if ($take >= 0) {
            $branches = $branches->skip($skip)->take($take);
        }

        if ($field = $request->get("field")) {
            $branches = $branches->orderByRaw("FIELD(branches.id,$field) DESC");
        }

        list($sort, $sortDir) = Paginator::getSorting($request);
        $branches = $branches->orderBy($sort, $sortDir);

        $pagesCount = Paginator::pagesCount($take, $totalCount);

        $branches = $branches->withCount('orders')->get();

        return $this->resourceListResponse('branches', $branches, $totalCount, $pagesCount);
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
    public function store(BranchRequest $request): JsonResponse
    {
        $branch = new Branch($request->all());
        $branch->save();

        if ($branch->is_default) {
            Branch::query()
                ->where("id", "!=", $branch->id)
                ->update(['is_default' => false]);
        }

        return $this->resourceItemResponse('branch', $branch);
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BranchRequest $request, Branch $branch): JsonResponse
    {
        $branch->fill($request->all());
        $branch->save();

        if ($branch->is_default) {
            Branch::query()
                ->where("id", "!=", $branch->id)
                ->update(['is_default' => false]);
        }

        return $this->resourceItemResponse('branch', $branch);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch): JsonResponse
    {
        if ($branch->is_default) {
            Branch::query()
                ->where("id", "!=", $branch->id)
                ->limit(1)
                ->update(['is_default' => true]);
        }
        $branch->delete();
        return $this->emptySuccessResponse();
    }
}
