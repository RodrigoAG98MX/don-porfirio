<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        Inertia::share([
            'modelData' => [
                [
                    'key' => 'name',
                    'title' => 'Nombre',
                    'align' => 'center',
                    'sortable' => false
                ],
                [
                    'key' => 'actions',
                    'title' => '...',
                    'align' => 'center',
                    'sortable' => false
                ],
            ],
        ]);
    }

    public function index(Request $request)
    {
        if (auth()->user()->cannot('browse-roles')) abort(403);
        $perPage = $request->query('perPage', 5);

        $roles = Role::query();

        if ($request->query('search')) {
            $value = '%' . $request->query('search') . '%';
            $roles->where('name', 'LIKE', $value);
        }

        return Inertia::render('Roles/Index', [
            'roles' => RoleResource::collection($roles->paginate($perPage)),
            'users' => User::get(),
            'permissions' => Permission::get()->pluck('name')->toArray(),
            'perPage' => (int)$perPage
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => $data['name']
            ]);
            $role->givePermissionTo($data['permissions']);

            foreach ($data['users'] as $user) {
                $admin = User::find($user);
                $admin->assignRole($role->name);
            }

            DB::commit();
            return redirect()->back()->with('success', sprintf('%s registrado', $role->name));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $role)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();

            $role->update([
                'name' => $data['name']
            ]);
            $role->syncPermissions($data['permissions']);
            $new_usuarios = User::whereIn('id', $data['users'])->get();
            foreach ($new_usuarios as $user) {
                $user->assignRole($role->name);
            }

            if ($role->users->diff($new_usuarios)->isNotEmpty()) {
                foreach ($role->users->diff($new_usuarios) as $usuario) {
                    $usuario->removeRole($role->name);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', sprintf('%s actualizado', $role->name));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        try {
            throw_if(in_array($role->name, ['Administrador', 'Mesero']), 'No es posible eliminar el role');

            DB::beginTransaction();

            $role->delete();

            DB::commit();
            return redirect()->back()->with('success', sprintf('%s registrado', $role->name));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
