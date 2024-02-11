<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Platillo extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'preparation_time',
        'cost',
        'price',
        'sucursal',
    ];

    public static function modelData()
    {
        return [
            [
                'key' => 'name',
                'title' => 'Nombre',
                'align' => 'center',
                'sortable' => false
            ],
            [
                'key' => 'description',
                'title' => 'Descripción',
                'align' => 'center',
                'sortable' => false
            ],
            [
                'key' => 'cost',
                'title' => 'Costo',
                'align' => 'center',
                'sortable' => false
            ],
            [
                'key' => 'price',
                'title' => 'Precio',
                'align' => 'center',
                'sortable' => false
            ],
            [
                'key' => 'actions',
                'title' => '...',
                'align' => 'center',
                'sortable' => false
            ],
        ];
    }
}
