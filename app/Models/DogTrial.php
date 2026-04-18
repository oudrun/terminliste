<?php
declare(strict_types=1);

namespace App\Models;

final class DogTrial
{
    public function __construct(
        public int $id,
        public string $trialNumber,
        public string $title,
        public string $organizer,
        public string $location,
        public string $trialPlace,
        public string $dateStart,
        public string $dateEnd,
        public string $registrationDeadline,
        public string $description,
        public string $contactPerson,
        public string $contactPhone,
        public string $contactEmail,
        public string $responsibleClub
    ) {
    }
}
