<?php
declare(strict_types=1);

namespace App\Models;

final class TrialClass
{
    public function __construct(
        public int $id,
        public int $trialId,
        public string $name,
        public ?string $startTime,
        public string $judge,
        public float $price,
        public int $maxParticipants,
        public ?string $notes
    ) {
    }
}
