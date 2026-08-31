<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $connection = 'assetdbgum';
    protected $table = 'assets';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'asset_number',
        'asset_name',
        'category',
    ];
}
