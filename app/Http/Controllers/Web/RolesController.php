<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $orderBy = $request->input('sortBy', 'roles.id');
        $order = $request->input('order', 'desc');
        $deleted = $request->input('deleted', 0);

        $campos = [
            'roles.id as id',
            'name as nombre',
            'permissions as permisos',
            'roles.deleted_at as fue_eliminado',
        ];

        $queryBuilder = $deleted ? Role::onlyTrashed() : Role::withoutTrashed();

        $queryBuilder = $queryBuilder->select($campos)
            ->orderBy($orderBy, $order);

        if ($query = $request->input('query', false)){
            $queryBuilder->where(function ($q) use ($query){
                $q->where('roles.name', 'like', '%'.$query.'%')
                    ->orWhere('slug', 'like', '%'.$query.'%');
            });
        }

        if ($perPage = $request->input('perPage', false)){
            $data = $queryBuilder->paginate($perPage);
        }else{
            $data = $queryBuilder->get();
        }

        return response()->success($data);

    }
}
