<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class IpDevice extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'brand', 'specifications', 'ip_address', 'location'];
}
