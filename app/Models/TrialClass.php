<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class TrialClass extends Model
{
    use HasFactory;

    protected $table = 'trial_classes';

    protected $fillable = [
        'trial_id',
        'name',
        'start_time',
        'judge',
        'price',
        'max_participants',
        'notes',
    ];

    public function trial(): BelongsTo
    {
        return $this->belongsTo(DogTrial::class, 'trial_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'class_id');
    }
}
