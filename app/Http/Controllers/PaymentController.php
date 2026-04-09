<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use Illuminate\Support\Facades\Cache;

class PaymentController extends Controller
{
    public function store(StorePaymentRequest $request)
    {
        $idempotencyKey = $request->header('Idempotency-Key');

        if (Cache::has($idempotencyKey)) {
            return response()->json([
                'success'  => true,
                'message'  => 'Pago recuperado del cache',
                'data'     => Cache::get($idempotencyKey),
                'replayed' => true,
            ]);
        }

        $payment = Payment::create([
            'amount' => $request->amount,
            'currency' => $request->currency,
            'status' => 'accepted',
            'description' => $request->description,
        ]);

        Cache::put($idempotencyKey, $payment, now()->addHours(24));

        return response()->json([
            'success'  => true,
            'message'  => 'Pago procesado',
            'data'     => $payment,
            'replayed' => false,
        ], 201);
    }
}
