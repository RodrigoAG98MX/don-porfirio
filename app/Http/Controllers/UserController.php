<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\In;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        Inertia::share([
            'modelData' => User::modelData(),
        ]);
    }

    public function index(Request $request)
    {
        if (auth()->user()->cannot('browse-users')) abort(403);
        $perPage = $request->query('perPage', 5);

        $users = User::query();

        if ($request->query('search')) {
            $value = '%' . $request->query('search') . '%';
            $users->where('name', 'LIKE', $value)
                ->orWhere('email', 'LIKE', $value);
        }

        return Inertia::render('Users/Index', [
            'users' => UserResource::collection($users->paginate($perPage)),
            'perPage' => (int)$perPage
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => sprintf('%s %s', $data['name'], $data['first_name']),
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'nss' => $data['nss'],
                'rfc' => $data['rfc'],
                'salary' => $data['salary'],
                'email_verified_at' => now(),
                'password' => Hash::make(sprintf('%s.%s', strtolower($data['name']), strtolower($data['first_name']))),
            ]);

            $user->address()->create([
                'street' => $data['street'],
                'number' => $data['number'],
                'suburb' => $data['suburb'],
                'cp' => $data['cp'],
                'references' => $data['references'],
                'state' => $data['state'],
                'country' => $data['country'],
            ]);

            DB::commit();
            return redirect()->back()->with('success', sprintf('Usuario %s registrado', $user->name));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();

            $user->update([
                'name' => sprintf('%s %s', $data['name'], $data['first_name']),
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'nss' => $data['nss'],
                'rfc' => $data['rfc'],
                'salary' => $data['salary'],
                'password' => Hash::make(sprintf('%s.%s', strtolower($data['name']), strtolower($data['first_name']))),
            ]);

            $user->address()->update([
                'street' => $data['street'],
                'number' => $data['number'],
                'suburb' => $data['suburb'],
                'cp' => $data['cp'],
                'references' => $data['references'],
                'state' => $data['state'],
                'country' => $data['country'],
            ]);

            DB::commit();
            return redirect()->back()->with('success', sprintf('Usuario %s actualizado', $user->name));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            throw_if($user->email === 'admin@donporfirio.com', 'No se puede eliminar al administrador.');

            $user->address()->delete();
            $user->delete();

            DB::commit();
            return redirect()->back()->with('success', sprintf('Usuario %s eliminado', $user->name));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
