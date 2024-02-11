<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Adress extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'street',
        'number',
        'suburb',
        'cp',
        'references',
        'state',
        'country',
    ];

    public function model()
    {
        return $this->morphTo();
    }
}
