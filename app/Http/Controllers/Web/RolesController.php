<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\RolesRequest;
use App\Models\Role;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function index(Request $request)iesoluciones

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

    public function store(RolesRequest $request)
    {
        $data = $request->except('huesped');

        DB::beginTransaction();
        try {
            $credentials = [
                'name' => $data['name'],
                'slug' => $data['slug'],
                'permissions' => $data['permissions'],
            ];

            $role = Sentinel::getRoleRepository()->createModel()->create($credentials);

            DB::commit();
            return response()->success($role);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->unprocessable('Error', ['Error al guardar al usuario.']);
        }
    }


    public function update(RolesRequest $request, $id)
    {

        $role = Role::query()->findOrFail($id);
        $data = $request->all();

        DB::beginTransaction();
        try {
            $role->update($data);

            DB::commit();
            return response()->success($role);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->unprocessable('Error', ['Error al actualizar el rol.']);
        }
    }

    public function show($id)
    {
        $rol = Role::with('users')
            ->select('id', 'name as nombre', 'slug', 'permissions as permisos')
            ->where('id', $id)
            ->get();

        return response()->success($rol);
    }

    public function destroy($id)
    {
        if ($id == Auth::id()) {
            return response()->unprocessable('Error', ['No es posible eliminar su propio rol.']);
        }

        $rol = Role::withTrashed()->findOrfail($id);
        if ($rol->deleted_at) {
            $rol->restore();
            return response()->success(['result' => 'El rol ha sido restaurado.']);
        } else {
            $rol->delete();
            return response()->success(['result' => 'El rol ha sido eliminado.']);
        }
    }

}
