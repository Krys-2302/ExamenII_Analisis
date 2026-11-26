<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
  protected $fillable = ['nombre', 'precio_por_persona', 'duracion_horas'];

    public function salidas(): HasMany
    {
        return $this->hasMany(Salida::class);
    }
}
