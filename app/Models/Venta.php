<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'number_table',
        'user_id',
        'propina',
        'date',
        'time',
        'sucursal_id'
    ];

    public static function modelData()
    {
        return [
            [
                'key' => 'sucursal',
                'title' => 'Sucursal',
                'align' => 'center',
                'sortable' => false
            ],
            [
                'key' => 'mesero',
                'title' => 'Mesero',
                'align' => 'center',
                'sortable' => false
            ],
            [
                'key' => 'mesa',
                'title' => 'Mesa',
                'align' => 'center',
                'sortable' => false
            ],
            [
                'key' => 'monto',
                'title' => 'Monto',
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

    public function mesero()
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'id');
    }

    public function consumo()
    {
        return $this->hasMany(Consumo::class, 'venta_id', 'id');
    }
}
