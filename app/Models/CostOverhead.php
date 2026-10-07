<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CostOverhead extends Model
{
    use HasUuids;

    protected $fillable = [
        'gl_account',
        'gl_name',
        'posting_date',
        'amount',
        'text',
        'document_header',
        'profit_center',
        'company_code',
        'departemen',
        'user',
        'activity',
    ];

    protected $casts = [
        'posting_date' => 'date',
        'amount'       => 'integer',
    ];
}
