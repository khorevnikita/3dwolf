<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeliveryAddress\AddressRequest;
use App\Models\DeliveryAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = DeliveryAddress::query();
        if ($search = $request->get("search")) {
            $addresses = $addresses->search($search);
        }
        $addresses = $addresses->get();

        return $this->resourceListResponse('deliveryAddresses', $addresses, count($addresses), 1);
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
    public function store(AddressRequest $request): JsonResponse
    {
        $address = new DeliveryAddress($request->all());
        $address->save();
        return $this->resourceItemResponse('deliveryAddress', $address);
    }

    /**
     * Display the specified resource.
     */
    public function show(DeliveryAddress $deliveryAddress): JsonResponse
    {
        return $this->resourceItemResponse('deliveryAddress', $deliveryAddress);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeliveryAddress $deliveryAddress)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AddressRequest $request, DeliveryAddress $deliveryAddress): JsonResponse
    {
        $deliveryAddress->fill($request->all());
        $deliveryAddress->save();
        return $this->resourceItemResponse('deliveryAddress', $deliveryAddress);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeliveryAddress $deliveryAddress): JsonResponse
    {
        $deliveryAddress->delete();

        return $this->emptySuccessResponse();
    }
}
