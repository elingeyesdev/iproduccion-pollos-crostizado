<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Merma extends Model
{
    protected $fillable = ['produccion_id', 'cantidad', 'estado'];

    public function produccion(): BelongsTo
    {
        return $this->belongsTo(Produccion::class);
    }
}
