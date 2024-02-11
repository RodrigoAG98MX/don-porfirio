<?php

namespace App\Http\Controllers;

use App\Http\Resources\SucursalResource;
use App\Models\Sucursal;
use App\Http\Requests\StoreSucursalRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SucursalController extends Controller
{
    public function __construct()
    {
        Inertia::share([
            'modelData' => Sucursal::modelData(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (auth()->user()->cannot('browse-sucursales')) abort(403);
        $perPage = $request->query('perPage', 5);

        $sucursales = Sucursal::query();

        if ($request->query('search')) {
            $value = '%' . $request->query('search') . '%';
            $sucursales->where('name', 'LIKE', $value)
                ->orWhere('email', 'LIKE', $value);
        }

        return Inertia::render('Sucursales/Index', [
            'sucursales' => SucursalResource::collection($sucursales->paginate($perPage)),
            'perPage' => (int)$perPage
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSucursalRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();

            $sucursal = Sucursal::create([
                'email' => $data['email'],
                'name' => $data['name'],
                'telephone' => $data['telephone'],
                'tables' => $data['tables'],
            ]);

            $sucursal->address()->create([
                'street' => $data['street'],
                'number' => $data['number'],
                'suburb' => $data['suburb'],
                'cp' => $data['cp'],
                'references' => $data['references'],
                'state' => $data['state'],
                'country' => $data['country'],
            ]);

            DB::commit();
            return redirect()->back()->with('success', sprintf('Sucursal %s registrada', $sucursal->name));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreSucursalRequest $request, Sucursal $sucursale)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();

            $sucursale->update([
                'email' => $data['email'],
                'name' => $data['name'],
                'telephone' => $data['telephone'],
                'tables' => $data['tables'],
            ]);

            $sucursale->address()->update([
                'street' => $data['street'],
                'number' => $data['number'],
                'suburb' => $data['suburb'],
                'cp' => $data['cp'],
                'references' => $data['references'],
                'state' => $data['state'],
                'country' => $data['country'],
            ]);

            DB::commit();
            return redirect()->back()->with('success', sprintf('Sucursal %s actualizada', $sucursale->name));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sucursal $sucursale)
    {
        try {
            DB::beginTransaction();

            $sucursale->address()->delete();
            $sucursale->delete();

            DB::commit();
            return redirect()->back()->with('success', sprintf('Sucursal %s eliminada', $sucursale->name));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
