<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitudes';
    use HasFactory;

    protected $fillable = [
        'participe_id',
        'estado',
        'demandante',
        'demandado',
    ];

    public function participe()
    {
        return $this->belongsTo(Participe::class);
    }

    public function archivos()
    {
        return $this->hasMany(SolicitudArchivo::class);
    }
}
