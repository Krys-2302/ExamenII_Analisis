<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = ['nombre', 'correo'];

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }
}
