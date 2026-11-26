<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reserva extends Model
{
   protected $fillable = ['cliente_id', 'fecha', 'total'];
    protected $casts = ['fecha' => 'date'];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemReserva::class);
    }

    public function salidas(): BelongsToMany
    {
        return $this->belongsToMany(Salida::class, 'item_reservas')
            ->withPivot(['cantidad_personas', 'precio_unitario'])
            ->withTimestamps();
    }
}
