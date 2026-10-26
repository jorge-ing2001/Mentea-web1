<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ClinicalEvolution extends Model
{
    use Auditable;

    protected $fillable = [
        'appointment_id', 'author_id', 'content', 'attachment_path',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function hasAttachment(): bool
    {
        return $this->attachment_path && Storage::disk('local')->exists($this->attachment_path);
    }
}
