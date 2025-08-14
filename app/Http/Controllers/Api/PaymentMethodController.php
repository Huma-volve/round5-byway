<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\SetupIntent;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Stripe\PaymentMethod as StripePaymentMethod;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function createSetupIntent(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $user = $request->user();

        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
            ]);
            $user->update(['stripe_customer_id' => $customer->id]);
        }

        $intent = SetupIntent::create([
            'customer' => $user->stripe_customer_id,
        ]);

     
        return ApiResponse::sendResponse(200, 'Setup Intent created successfully', [
            'client_secret' => $intent->client_secret,
        ]);
    }

    public function index()
    {
        $user = request()->user();
        $paymentMethods = PaymentMethod::where('user_id', $user->id)->get();
        return ApiResponse::sendResponse(200, 'Payment methods retrieved successfully', $paymentMethods);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));
        $user = $request->user();

        // جلب الـ PaymentMethod من Stripe
        $pm = StripePaymentMethod::retrieve($request->payment_method);

        // attach لو مش attach
        if (empty($pm->customer)) {
            $pm->attach(['customer' => $user->stripe_customer_id]);
        }

        // خزن السجل محلياً (billing_details مش حساسة هنا)
        $record = PaymentMethod::create([
            'user_id' => $user->id,
            'stripe_pm_id' => $pm->id,
            'brand' => $pm->card->brand ?? null,
            'last4' => $pm->card->last4 ?? null,
            'billing_details' => $pm->billing_details ? (array)$pm->billing_details : null,

        ]);

        return response()->json([
            'status' => 'success',
            'payment_method' => $record,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = request()->user();
        $paymentMethod = PaymentMethod::where('user_id', $user->id)->where('id', $id)->firstOrFail();
        Stripe::setApiKey(config('services.stripe.secret'));
        StripePaymentMethod::retrieve($paymentMethod->stripe_pm_id)->detach();
        $paymentMethod->delete();
        return ApiResponse::sendResponse(200, 'Payment method deleted successfully');
    }
}
