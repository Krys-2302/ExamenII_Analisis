<?php

namespace App\Services;

use App\Exceptions\CuposInsuficientesException;
use App\Models\Reserva;
use App\Models\Salida;
use Illuminate\Support\Facades\DB;

class ServicioReservas
{
    /**
     * Create a new class instance.
     */
    public function reservar(int $clienteId, array $items): Reserva
    {
        return DB::transaction(function () use ($clienteId, $items) {
            $reserva = Reserva::create([
                'cliente_id' => $clienteId,
                'fecha' => now()->toDateString(),
                'total' => 0,
            ]);

            $total = 0;

            // Orden fijo por salida_id para evitar deadlocks entre reservas concurrentes
            foreach (collect($items)->sortBy('salida_id') as $item) {
                $cantidad = (int) $item['cantidad_personas'];
                $salida = Salida::with('tour')->findOrFail($item['salida_id']);

                // Descuento atómico: solo actualiza si aún hay cupos suficientes
                $afectadas = Salida::where('id', $salida->id)
                    ->where('cupos_disponibles', '>=', $cantidad)
                    ->decrement('cupos_disponibles', $cantidad);

                if ($afectadas === 0) {
                    throw new CuposInsuficientesException(
                        "La salida {$salida->id} no tiene cupos suficientes."
                    );
                }

                $precio = $salida->tour->precio_por_persona; // precio vigente, queda congelado

                $reserva->items()->create([
                    'salida_id' => $salida->id,
                    'cantidad_personas' => $cantidad,
                    'precio_unitario' => $precio,
                ]);

                $total += $precio * $cantidad;
            }

            $reserva->update(['total' => $total]);

            return $reserva->load('items');
        });
    }
}
