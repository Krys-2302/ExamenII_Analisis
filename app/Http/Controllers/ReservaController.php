<?php

namespace App\Http\Controllers;
use App\Exceptions\CuposInsuficientesException;
use App\Services\ServicioReservas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function store(Request $request, ServicioReservas $servicio): JsonResponse
    {
        $datos = $request->validate([
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.salida_id' => ['required', 'integer', 'distinct', 'exists:salidas,id'],
            'items.*.cantidad_personas' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $reserva = $servicio->reservar($datos['cliente_id'], $datos['items']);
        } catch (CuposInsuficientesException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($reserva, 201);
    }
}
