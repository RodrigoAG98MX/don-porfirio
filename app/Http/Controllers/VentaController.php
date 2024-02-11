<?php

namespace App\Http\Controllers;

use App\Http\Requests\VentaRequest;
use App\Http\Resources\SucursalResource;
use App\Http\Resources\VentaResource;
use App\Models\Platillo;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Venta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VentaController extends Controller
{
    public function __construct()
    {
        Inertia::share([
            'modelData' => Venta::modelData(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('perPage', 5);

        $ventas = Venta::query();

        /*if ($request->query('search')) {
            $value = '%' . $request->query('search') . '%';
            $ventas->where('name', 'LIKE', $value)
                ->orWhere('email', 'LIKE', $value);
        }*/

        return Inertia::render('Ventas/Index', [
            'ventas' => VentaResource::collection($ventas->paginate($perPage)),
            'sucursales' => SucursalResource::collection(Sucursal::get()),
            'meseros' => auth()->user()->hasRole('Mesero') ? User::where('id', auth()->user()->id)->get() : User::role('Mesero')->get(),
            'perPage' => (int)$perPage
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VentaRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();

            Venta::create([
                'number_table' => $data['number_table'],
                'user_id' => $data['user_id'],
                'propina' => $data['propina'],
                'date' => $data['date'],
                'time' => $data['time'],
                'sucursal_id' => $data['sucursal_id'],
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Venta registrada');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venta $venta)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();

            $venta->update([
                'number_table' => $data['number_table'],
                'user_id' => $data['user_id'],
                'propina' => $data['propina'],
                'date' => $data['date'],
                'time' => $data['time'],
                'sucursal_id' => $data['sucursal_id'],
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Venta actualizada');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venta $venta)
    {
        try {
            DB::beginTransaction();

            $venta->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Venta eliminada');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
