<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeTurbine extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'conc',
        'unit',
        'load_mw',
        'status',
        'date_on',
        'date_off',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'date_on' => 'date',
        'date_off' => 'date',
        'load_mw' => 'float',
    ];
}
