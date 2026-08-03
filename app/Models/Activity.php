<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasUuids;

    protected $fillable = [
        'ticket_number', 'title', 'description', 'category', 'sub_category', 'device_type', 'barcode_number',
        'priority', 'status', 'department',
        'reporter_name', 'reporter_phone', 'latitude', 'longitude', 'photo_path',
        'assigned_to', 'team_id', 'created_by', 'completed_at'
    ];
    protected function casts(): array { return ['completed_at' => 'datetime', 'latitude' => 'decimal:8', 'longitude' => 'decimal:8']; }

    protected static function booted(): void
    {
        static::creating(function (Activity $activity) {
            if (!$activity->ticket_number) {
                $activity->ticket_number = 'TKT-' . strtoupper(Str::random(8));
            }
        });
    }

    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function team(): BelongsTo { return $this->belongsTo(Team::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function logs(): HasMany { return $this->hasMany(ActivityLog::class); }
}
