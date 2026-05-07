<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'location',
        'quantity',
        'unit',
        'min_threshold',
        'unit_cost',
        'last_restocked_at',
    ];

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
