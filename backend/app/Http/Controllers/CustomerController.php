<?php

namespace App\Http\Controllers;

use App\Helpers\Paginator;
use App\Http\Requests\Customer\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        list($page, $skip, $take) = Paginator::get($request);
        $models = Customer::query();

        if ($request->has('search')) {
            // todo: localScope
            $search = $request->get('search');
            $models = $models->search($search);
        }

        if ($type = $request->get('type')) {
            $models = $models->whereType($type);
        }

        if ($entityType = $request->get('entity_type')) {
            $models = $models->whereEntityType($entityType);
        }


        $totalCount = $models->count();

        if ($take >= 0) {
            $models = $models->skip($skip)->take($take);
        }

        list($sort, $sortDir) = Paginator::getSorting($request);
        if ($field = $request->get("field")) {
            $models = $models->orderByRaw("FIELD(customers.id,$field) DESC");
        }
        $models = $models->orderBy($sort, $sortDir);

        $models = $models->get();
        $pagesCount = Paginator::pagesCount($take, $totalCount);
        return $this->resourceListResponse('customers', $models, $totalCount, $pagesCount);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @param CustomerRequest $request
     * @return JsonResponse
     */
    public function store(CustomerRequest $request): JsonResponse
    {
        $model = new Customer($request->all());
        $model->save();
        return $this->resourceItemResponse('customer', $model);
    }

    /**
     * @param Customer $customer
     * @return JsonResponse
     */
    public function show(Customer $customer): JsonResponse
    {
        return $this->resourceItemResponse('customer', $customer);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * @param CustomerRequest $request
     * @param Customer $customer
     * @return JsonResponse
     */
    public function update(CustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer->fill($request->all());
        $customer->save();
        return $this->resourceItemResponse('customer', $customer);
    }

    /**
     *
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();
        return $this->emptySuccessResponse();
    }
}
