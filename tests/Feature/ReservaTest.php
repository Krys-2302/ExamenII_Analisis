<?php

use App\Models\Cliente;
use App\Models\ItemReserva;
use App\Models\Reserva;
use App\Models\Salida;
use App\Models\Tour;

function crearSalida(Tour $tour, int $cupos): Salida
{
    return Salida::create([
        'tour_id' => $tour->id,
        'fecha' => now()->addDays(5)->toDateString(),
        'cupos_disponibles' => $cupos,
    ]);
}

test('una reserva válida con dos salidas se registra y descuenta los cupos', function () {
    $cliente = Cliente::create(['nombre' => 'Ana', 'correo' => 'ana@example.com']);
    $tour = Tour::create(['nombre' => 'Canopy', 'precio_por_persona' => 50, 'duracion_horas' => 3]);
    $s1 = crearSalida($tour, 10);
    $s2 = crearSalida($tour, 8);

    $response = $this->postJson('/reservas', [
        'cliente_id' => $cliente->id,
        'items' => [
            ['salida_id' => $s1->id, 'cantidad_personas' => 2],
            ['salida_id' => $s2->id, 'cantidad_personas' => 3],
        ],
    ]);

    $response->assertCreated();
    expect(Reserva::count())->toBe(1);
    expect(ItemReserva::count())->toBe(2);
    expect((float) Reserva::first()->total)->toBe(250.0);
    expect($s1->fresh()->cupos_disponibles)->toBe(8);
    expect($s2->fresh()->cupos_disponibles)->toBe(5);
});

test('si la segunda salida no tiene cupos no se registra nada', function () {
    $cliente = Cliente::create(['nombre' => 'Luis', 'correo' => 'luis@example.com']);
    $tour = Tour::create(['nombre' => 'Aguas termales', 'precio_por_persona' => 40, 'duracion_horas' => 2]);
    $s1 = crearSalida($tour, 10);
    $s2 = crearSalida($tour, 1);

    $response = $this->postJson('/reservas', [
        'cliente_id' => $cliente->id,
        'items' => [
            ['salida_id' => $s1->id, 'cantidad_personas' => 2],
            ['salida_id' => $s2->id, 'cantidad_personas' => 3],
        ],
    ]);

    $response->assertStatus(422);
    expect(Reserva::count())->toBe(0);
    expect(ItemReserva::count())->toBe(0);
    expect($s1->fresh()->cupos_disponibles)->toBe(10);
    expect($s2->fresh()->cupos_disponibles)->toBe(1);
});
