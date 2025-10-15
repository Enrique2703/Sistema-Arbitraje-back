<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participe extends Model
{
    protected $table = 'participes';

    protected $fillable = [
        'credencial_id',
        'nombres',
        'estado'
    ];

    public function credencial()
    {
        return $this->belongsTo(Credencial::class, 'credencial_id');
    }
}