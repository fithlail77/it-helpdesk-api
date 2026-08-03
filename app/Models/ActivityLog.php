<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasUuids;

    protected $fillable = ['activity_id', 'user_id', 'status', 'note', 'repair_data', 'sparepart_id', 'sparepart_quantity', 'sparepart_price'];

    protected function casts(): array
    {
        return ['repair_data' => 'array', 'sparepart_price' => 'decimal:2'];
    }

    public function activity(): BelongsTo { return $this->belongsTo(Activity::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function sparepart(): BelongsTo { return $this->belongsTo(Sparepart::class); }
}
