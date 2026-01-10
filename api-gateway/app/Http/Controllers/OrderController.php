<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        // Validar el payload
        $validated = $request->validate([
            'order_id' => 'required|uuid',
            'customer_id' => 'required|uuid',
            'total_amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'items' => 'required|array|min:1',
            'items.*.sku' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0.01',
        ]);

        // Reenviar la solicitud al Order Service
        $response = Http::post(env('ORDER_SERVICE_URL') . '/orders', $validated);

        // Retornar la respuesta del Order Service
        if ($response->successful()) {
            return response()->json([
                'request_id' => $request->header('X-Request-Id'),
                'status' => 'accepted',
            ], 202);
        }

        // Manejo de errores
        return response()->json([
            'request_id' => $request->header('X-Request-Id'),
            'error' => 'Failed to process the order',
        ], $response->status());
    }
}
