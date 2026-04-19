<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Address\ListAddressesRequest;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Http\Requests\Address\SetDefaultAddressRequest;
use App\Http\Resources\AddressResource;
use App\Services\AddressService;
use App\DTOs\StoreAddressDTO;
use App\DTOs\UpdateAddressDTO;
use App\Models\Address;
use App\Traits\ApiResponserTrait;

class AddressController extends Controller
{
    use ApiResponserTrait;

    public function __construct(
        private AddressService $addressService,
    ) {}

    public function index(ListAddressesRequest $request)
    {
        $addresses = $this->addressService->getUserAddresses(
            $request->user()->id,
            $request->input('type')
        );

        return $this->paginated($addresses, 'Addresses retrieved successfully');
    }

    public function store(StoreAddressRequest $request)
    {
        $address = $this->addressService->storeAddress(
            StoreAddressDTO::fromRequest($request),
            $request->user()->id
        );

        return $this->success(new AddressResource($address), __('general.address_added'), 201);
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        $this->authorize('update', $address);

        $updated = $this->addressService->updateAddress(
            $address,
            UpdateAddressDTO::fromRequest($request)
        );

        return $this->success(new AddressResource($updated), __('general.address_updated'));
    }

    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);

        $this->addressService->deleteAddress($address);

        return $this->success(null, __('general.address_deleted'));
    }

    public function setDefault(SetDefaultAddressRequest $request, Address $address)
    {
        $this->authorize('update', $address);

        $this->addressService->setAsDefault($address);

        return $this->success(null, __('general.address_set_default'));
    }
}