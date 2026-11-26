<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salida extends Model
{
   protected $fillable = ['tour_id', 'guia_id', 'fecha', 'cupos_disponibles'];
    protected $casts = ['fecha' => 'date'];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function guia(): BelongsTo
    {
        return $this->belongsTo(Guia::class);
    }
}
