<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->hasRole('Administrador')) {
            $ventas = Venta::query();

            $ventas->select(
                DB::raw('HOUR(time) as hora'),
                DB::raw('SUM(amount) as monto')
            );
            $groupBy = ['hora'];
            $querys = [];

            $date = Carbon::parse($request->query('fecha'))->format('Y-m-d') ?? now()->format('Y-m-d');

            if ($request->query('fecha')) {
                $ventas->whereDate('date', $date);
                $querys['fecha'] = $date;
            }
            if ($request->query('mesero')) {
                $ventas->where('user_id', $request->query('mesero'));
                $querys['mesero'] = $request->query('mesero');
            }
            if ($request->query('mesa')) {
                $ventas->where('number_table', $request->query('mesa'));
                $querys['mesa'] = $request->query('mesa');
            }
            if ($request->query('monto')) {
                $ventas->where('amount', 'like', '%' . $request->query('monto'));
                $querys['monto'] = $request->query('monto');
            }

            $ventas->groupBy($groupBy);
            $ventas->orderBy('hora');
            $ventas = $ventas->get();
            $labels = $ventas->pluck('hora')->transform(function ($item) {
                return sprintf('%s:00', $item);
            });

            return Inertia::render('Dashboard', [
                'ventas' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'Ventas',
                            'data' => $ventas->pluck('monto')->toArray(),
                            'backgroundColor' => '#ED722F',
                        ]
                    ]
                ],
                'meseros' => User::role('Mesero')->get(),
                'querys' => $querys
            ]);
        } else {
            return Inertia::render('Home');
        }
    }

    public function pdf(Request $request)
    {
        $ventas = Venta::query();

        $ventas->select(
            DB::raw('HOUR(time) as hora'),
            DB::raw('SUM(amount) as monto')
        );
        $groupBy = ['hora'];

        $date = Carbon::parse($request->query('fecha'))->format('Y-m-d') ?? now()->format('Y-m-d');

        if ($request->query('fecha')) {
            $ventas->whereDate('date', $date);
        }
        if ($request->query('mesero')) {
            $ventas->where('user_id', $request->query('mesero'));
        }
        if ($request->query('mesa')) {
            $ventas->where('number_table', $request->query('mesa'));
        }
        if ($request->query('monto')) {
            $ventas->where('amount', 'like', '%' . $request->query('monto'));
        }

        $ventas->groupBy($groupBy);
        $ventas->orderBy('hora');
        $ventas = $ventas->get();
        $labels = $ventas->pluck('hora')->transform(function ($item) {
            return sprintf('%s:00', $item);
        })->toArray();
        $data = $ventas->pluck('monto')->toArray();
        $color = '#ED722F';
        $labels = implode('|', $labels);
        $data = implode(',', $data);

        $pdf = PDF::loadView('pdf', [
            'labels' => $labels,
            'data' => $data,
            '$color' => $color,
        ]);
        return $pdf->download(sprintf('%s-ventas.pdf', $date));
    }
}
