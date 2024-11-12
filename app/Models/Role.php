<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'roles';

    protected $fillable = [
        'slug',
        'name',
        'permissions'
    ];

    public function users(){
        return $this->hasManyThrough(
            User::class,
            RoleUser::class,
            'role_id',
            'id',
            'id',
            'user_id'
        )->select(
            'id as id_usuario',
            'email as email_usuario',
            DB::raw("CONCAT_WS(' ', nombre, apellido_paterno, apellido_materno) as nombre_usuario"),
            'telefono as telefono_usuario'
        );
    }
}
