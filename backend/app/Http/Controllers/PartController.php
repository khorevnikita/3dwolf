<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\Part\PartRequest;
use App\Models\Part;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
                    ->orWhere("color", "like", "%$search%")
                    ->orWhere("prod_number", "like", "%$search%");
            });
        }

        if ($manufacturerId = $request->get("manufacturer_id")) {
            $models = $models->whereManufacturerId($manufacturerId);
        }

        if ($materialId = $request->get("material_id")) {
            $models = $models->whereMaterialId($materialId);
        }

        if ($statuses = array_filter(explode(',', $request->get("status")))) {
            $models = $models->whereIn("status", $statuses);
        }
        if ($prodNumber = $request->get("prod_number")) {
            $models = $models->where("prod_number", $prodNumber);
        }

        if ($request->get("not_ended")) {
            $models = $models->where("status", "!=", "ended");
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
        if ($count = (int)$request->get("count")) {
            // create multiple
            $parts = [];

            $mask = $request->get("inv_number");
            $maskNeedle = str_replace("%", "", $mask);
            $lastMaskItem = Part::query()
                ->where("prod_number", "=", $request->get("prod_number"))
                ->where("inv_number", "like", "%$maskNeedle%")
                ->orderBy("id", "desc")
                ->first();

            if (!$lastMaskItem) {
                $index = 1;
            } else {
                $index = (int)str_replace($maskNeedle, "", $lastMaskItem->inv_number) + 1;
               # var_dump("last index", $maskNeedle, $lastMaskItem->inv_number);
            }
            for ($i = 0; $i < $count; $i++) {
                $part = new Part($request->all());
                $part->inv_number = str_replace("%", $index, $mask);
                $part->save();
                $part->load(['manufacturer', 'material']);
                $index++;

                $parts[] = $part;
            }

            return $this->resourceListResponse('parts', $parts, count($parts), 1);
        } else {
            $part = new Part($request->all());
            $part->save();

            $part->load(['manufacturer', 'material']);
            return $this->resourceItemResponse('part', $part);
        }
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

        $part->load(['manufacturer', 'material']);
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

    public function exportAuth(Part $part): JsonResponse
    {
        auth('web')->login(auth('sanctum')->user());
        $hash = Hash::make("wolf-export-part-$part->id");
        return $this->resourceItemResponse('download_link', url("api/parts/$part->id/export/sticker?hash=$hash"));

    }

    public function exportSticker(Part $part, Request $request)
    {
        if (!Hash::check("wolf-export-part-$part->id", $request->get('hash'))) abort(403);
        $time = Carbon::now()->format("Y-m-d_H:i:s");
        $filename = "part_$part->id" . "_by_$time.pdf";

        $pdf = Pdf::loadView('exports.part_sticker', [
            'part' => $part,
        ])->setPaper('a10', 'landscape');
        return $pdf->download($filename);
    }
}
