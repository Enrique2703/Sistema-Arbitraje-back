<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudArchivo extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_archivos';

    protected $fillable = [
        'solicitud_id',
        'archivo_adjunto',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }
}
