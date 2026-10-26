<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Survey extends Model
{
    protected $fillable = ['appointment_id','patient_id','rating','comment','answers'];
    protected function casts(): array { return ['answers'=>'array','rating'=>'integer']; }
    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }
    public function patient(): BelongsTo { return $this->belongsTo(User::class, 'patient_id'); }
}
