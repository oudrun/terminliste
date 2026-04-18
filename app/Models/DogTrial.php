<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class DogTrial extends Model
{
    use HasFactory;

    protected $table = 'dog_trials';

    protected $fillable = [
        'trial_number',
        'title',
        'organizer',
        'location',
        'trial_place',
        'date_start',
        'date_end',
        'registration_deadline',
        'description',
        'contact_person',
        'contact_phone',
        'contact_email',
        'responsible_club',
    ];

    public function classes(): HasMany
    {
        return $this->hasMany(TrialClass::class, 'trial_id');
    }
}
