<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Registration extends Model
{
    use HasFactory;

    protected $table = 'registrations';

    protected $fillable = [
        'class_id',
        'owner_name',
        'email',
        'phone',
        'dog_name',
        'dog_regno',
        'dog_breed',
        'dog_class',
        'comment',
        'status',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(TrialClass::class, 'class_id');
    }
}
