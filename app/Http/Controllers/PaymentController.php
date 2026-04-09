<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use Illuminate\Support\Facades\Cache;

class PaymentController extends Controller
{
    public function store(StorePaymentRequest $request)
    {
        $payment = Payment::create([
            'amount' => $request->amount,
            'currency' => $request->currency,
            'status' => 'accepted',
            'description' => $request->description,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Pago procesado',
            'data'     => $payment,
            'replayed' => false,
        ], 201);
    }
}
