<?php
// app/Http/Controllers/Api/AddressController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'shipping');
        $addresses = auth()->user()->addresses()
            ->where('type', $type)
            ->get();

        return AddressResource::collection($addresses);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:shipping,billing',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        return DB::transaction(function () use ($request) {
            $user = auth()->user();

            if ($request->is_default) {
                $user->addresses()
                    ->where('type', $request->type)
                    ->update(['is_default' => false]);
            }

            $address = Address::create(array_merge(
                $request->all(),
                ['user_id' => $user->id]
            ));

            return response()->json([
                'data' => new AddressResource($address),
                'message' => 'Address added successfully'
            ], 201);
        });
    }

    public function update(Request $request, Address $address)
    {
        $this->authorize('update', $address);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        return DB::transaction(function () use ($request, $address) {
            $user = auth()->user();

            if ($request->is_default) {
                $user->addresses()
                    ->where('type', $address->type)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            $address->update($request->all());

            return response()->json([
                'data' => new AddressResource($address),
                'message' => 'Address updated successfully'
            ]);
        });
    }

    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);

        if ($address->is_default) {
            $newDefault = auth()->user()->addresses()
                ->where('type', $address->type)
                ->where('id', '!=', $address->id)
                ->first();

            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $address->delete();

        return response()->json([
            'message' => 'Address deleted successfully'
        ]);
    }

    public function setDefault(Address $address)
    {
        $this->authorize('update', $address);

        DB::transaction(function () use ($address) {
            auth()->user()->addresses()
                ->where('type', $address->type)
                ->update(['is_default' => false]);

            $address->update(['is_default' => true]);
        });

        return response()->json([
            'message' => 'Address set as default successfully'
        ]);
    }
}