<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $fillable = ['name', 'email', 'password', 'role', 'team_id', 'phone', 'avatar', 'is_active'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array { return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_active' => 'boolean']; }
    
    public function team(): BelongsTo { return $this->belongsTo(Team::class); }
    public function assignedActivities(): HasMany { return $this->hasMany(Activity::class, 'assigned_to'); }
    public function createdActivities(): HasMany { return $this->hasMany(Activity::class, 'created_by'); }
    public function activityLogs(): HasMany { return $this->hasMany(ActivityLog::class); }
}
