<?php
// app/Http/Controllers/Api/PaymentMethodController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = auth()->user()->paymentMethods()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return ApiResponse::success(PaymentMethodResource::collection($paymentMethods));
    }

    public function store(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|max:255',
            'payment_method_id' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'last_four' => 'required|string|size:4',
            'exp_month' => 'required|integer|min:1|max:12',
            'exp_year' => 'required|integer|min:' . date('Y'),
            'is_default' => 'boolean',
        ]);

        return DB::transaction(function () use ($request) {
            $user = auth()->user();

            if ($request->is_default) {
                $user->paymentMethods()->update(['is_default' => false]);
            }

            $paymentMethod = PaymentMethod::create(array_merge(
                $request->all(),
                ['user_id' => $user->id]
            ));

            return ApiResponse::success(new PaymentMethodResource($paymentMethod), 'Payment method added successfully', 201);
        });
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $this->authorize('delete', $paymentMethod);

        if ($paymentMethod->is_default) {
            $newDefault = auth()->user()->paymentMethods()
                ->where('id', '!=', $paymentMethod->id)
                ->first();

            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $paymentMethod->delete();

        return ApiResponse::success(null, 'Payment method deleted successfully');
    }

    public function setDefault(PaymentMethod $paymentMethod)
    {
        $this->authorize('update', $paymentMethod);

        DB::transaction(function () use ($paymentMethod) {
            auth()->user()->paymentMethods()->update(['is_default' => false]);
            $paymentMethod->update(['is_default' => true]);
        });

        return ApiResponse::success(null, 'Payment method set as default successfully');
    }
}