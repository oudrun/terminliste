<?php
declare(strict_types=1);

namespace App\Models;

final class Registration
{
    public function __construct(
        public int $id,
        public int $classId,
        public string $ownerName,
        public string $email,
        public string $phone,
        public string $dogName,
        public string $dogRegno,
        public string $dogBreed,
        public string $dogClass,
        public string $comment,
        public string $status
    ) {
    }
}
