<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlatilloRequest;
use App\Http\Resources\PlatilloResource;
use App\Models\Platillo;
use App\Models\Sucursal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PlatilloController extends Controller
{
    public function __construct()
    {
        Inertia::share([
            'modelData' => Platillo::modelData(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (auth()->user()->cannot('browse-platillos')) abort(403);

        $perPage = $request->query('perPage', 5);

        $platillos = Platillo::query();

        if ($request->query('search')) {
            $value = '%' . $request->query('search') . '%';
            $platillos->where('name', 'LIKE', $value)
                ->orWhere('price', 'LIKE', $value)
                ->orWhere('cost', 'LIKE', $value);
        }

        return Inertia::render('Platillo/Index', [
            'platillos' => PlatilloResource::collection($platillos->paginate($perPage)),
            'sucursales' => Sucursal::get(),
            'perPage' => (int)$perPage
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlatilloRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();

            $hr = $data['hr'] < 10 ? str_pad($data['hr'], 2, "0", STR_PAD_LEFT) : $data['hr'];
            $min = $data['min'] < 10 ? str_pad($data['min'], 2, "0", STR_PAD_LEFT) : $data['min'];
            $time = sprintf('%s:%s', $hr, $min);
            $platillo = Platillo::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'preparation_time' => $time,
                'cost' => $data['cost'],
                'price' => $data['price'],
                'sucursal' => json_encode($data['sucursal']),
            ]);

            DB::commit();
            return redirect()->back()->with('success', sprintf('Platillo %s registrado', $platillo->name));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlatilloRequest $request, Platillo $platillo)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();

            $platillo->update([
                'name' => $data['name'],
                'description' => $data['description'],
                'preparation_time' => $data['preparation_time'],
                'cost' => $data['cost'],
                'price' => $data['price'],
                'sucursal' => json_encode($data['sucursal']),
            ]);

            DB::commit();
            return redirect()->back()->with('success', sprintf('Platillo %s actualizado', $platillo->name));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Platillo $platillo)
    {
        try {
            DB::beginTransaction();

            $platillo->delete();

            DB::commit();
            return redirect()->back()->with('success', sprintf('Platillo %s eliminado', $platillo->name));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
