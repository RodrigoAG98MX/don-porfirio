<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sucursal extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'telephone',
        'email',
        'tables',
    ];

    public function address()
    {
        return $this->morphOne(Adress::class, 'model');
    }

    public function platillos()
    {
        return Platillo::whereJsonContains('sucursal', $this->id)->get();
    }

    public function tablesOp()
    {
        $tables = [];
        for ($i = 0; $i < $this->tables; $i++) {
            $tables[] = $i + 1;
        }
        return $tables;
    }

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
                'key' => 'email',
                'title' => 'Correo',
                'align' => 'center',
                'sortable' => false
            ],
            [
                'key' => 'telephone',
                'title' => 'Teléfono',
                'align' => 'center',
                'sortable' => false
            ],
            [
                'key' => 'tables',
                'title' => 'Mesas',
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
