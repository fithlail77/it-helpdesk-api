<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Sparepart extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'stock', 'price'];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'price' => 'decimal:2',
        ];
    }
}
