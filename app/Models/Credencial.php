<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Credencial extends Authenticatable implements JWTSubject
{
    protected $table = 'credenciales';

    protected $fillable = [
        'email',
        'password',
        'tipo_usuario',
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Obtiene el partícipe asociado a estas credenciales.
     */
    public function participe()
    {
        return $this->hasOne(Participe::class);
    }
    
    public function usuario()
    {
        return $this->hasOne(Usuario::class);
    }
}
