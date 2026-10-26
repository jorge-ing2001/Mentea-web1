<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Professional extends Model
{
    use Auditable, HasFactory, SoftDeletes;
    protected $fillable = ['user_id','name','specialty','profession','description','email','phone','photo','is_available','appointment_duration_minutes'];
    protected function casts(): array { return ['is_available'=>'boolean','appointment_duration_minutes'=>'integer']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function schedules(): HasMany { return $this->hasMany(Schedule::class); }
    public function appointments(): HasMany { return $this->hasMany(Appointment::class); }
    public function activeSchedules(): HasMany { return $this->schedules()->where('is_active', true); }
}
