<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;
use PDO;

final class TrialRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getPdo();
    }

    public function getAllTrials(): array
    {
        return $this->pdo->query('SELECT * FROM dog_trials ORDER BY date_start ASC')->fetchAll();
    }

    public function getClassesByTrial(int $trialId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.*, COUNT(r.id) AS registration_count
             FROM trial_classes c
             LEFT JOIN registrations r ON r.class_id = c.id AND r.status <> 'cancelled'
             WHERE c.trial_id = ?
             GROUP BY c.id
             ORDER BY c.start_time ASC"
        );

        $stmt->execute([$trialId]);

        return $stmt->fetchAll();
    }

    public function getClassWithTrial(int $classId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.*, t.title, t.organizer, t.location, t.trial_place, t.trial_number, t.contact_person,
                    t.contact_phone, t.contact_email, t.responsible_club, t.date_start, t.date_end,
                    t.registration_deadline, COUNT(r.id) AS registration_count
             FROM trial_classes c
             INNER JOIN dog_trials t ON t.id = c.trial_id
             LEFT JOIN registrations r ON r.class_id = c.id AND r.status <> 'cancelled'
             WHERE c.id = ?
             GROUP BY c.id, t.title, t.organizer, t.location, t.trial_place, t.trial_number, t.contact_person,
                      t.contact_phone, t.contact_email, t.responsible_club, t.date_start, t.date_end, t.registration_deadline"
        );

        $stmt->execute([$classId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function getAllClasses(): array
    {
        return $this->pdo->query(
            'SELECT c.*, t.title AS trial_title
             FROM trial_classes c
             INNER JOIN dog_trials t ON t.id = c.trial_id
             ORDER BY t.date_start ASC, c.start_time ASC'
        )->fetchAll();
    }

    public function getAllRegistrations(): array
    {
        return $this->pdo->query(
            "SELECT r.*, c.name AS class_name, t.title AS trial_title
             FROM registrations r
             INNER JOIN trial_classes c ON c.id = r.class_id
             INNER JOIN dog_trials t ON t.id = c.trial_id
             ORDER BY r.created_at DESC"
        )->fetchAll();
    }

    public function addTrial(array $data): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO dog_trials (
                trial_number, title, organizer, location, trial_place, date_start, date_end,
                registration_deadline, description, contact_person, contact_phone, contact_email, responsible_club
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            trim((string) ($data['trial_number'] ?? '')),
            trim((string) ($data['title'] ?? '')),
            trim((string) ($data['organizer'] ?? '')),
            trim((string) ($data['location'] ?? '')),
            trim((string) ($data['trial_place'] ?? '')),
            (string) ($data['date_start'] ?? ''),
            (string) ($data['date_end'] ?? ''),
            (string) ($data['registration_deadline'] ?? ''),
            trim((string) ($data['description'] ?? '')),
            trim((string) ($data['contact_person'] ?? '')),
            trim((string) ($data['contact_phone'] ?? '')),
            trim((string) ($data['contact_email'] ?? '')),
            trim((string) ($data['responsible_club'] ?? '')),
        ]);
    }

    public function addClass(array $data): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO trial_classes (trial_id, name, start_time, judge, price, max_participants, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            (int) ($data['trial_id'] ?? 0),
            trim((string) ($data['name'] ?? '')),
            ($data['start_time'] ?? '') ?: null,
            trim((string) ($data['judge'] ?? '')),
            (float) ($data['price'] ?? 0),
            (int) ($data['max_participants'] ?? 10),
            trim((string) ($data['notes'] ?? '')),
        ]);
    }

    public function addRegistration(int $classId, array $data): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO registrations (class_id, owner_name, email, phone, dog_name, dog_regno, dog_breed, dog_class, comment)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            $classId,
            trim((string) ($data['owner_name'] ?? '')),
            trim((string) ($data['email'] ?? '')),
            trim((string) ($data['phone'] ?? '')),
            trim((string) ($data['dog_name'] ?? '')),
            trim((string) ($data['dog_regno'] ?? '')),
            trim((string) ($data['dog_breed'] ?? '')),
            trim((string) ($data['dog_class'] ?? '')),
            trim((string) ($data['comment'] ?? '')),
        ]);
    }

    public function updateRegistrationStatus(int $registrationId, string $status): void
    {
        $stmt = $this->pdo->prepare('UPDATE registrations SET status = ? WHERE id = ?');
        $stmt->execute([$status, $registrationId]);
    }

    public function deleteTrial(int $trialId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM dog_trials WHERE id = ?');
        $stmt->execute([$trialId]);
    }

    public function deleteClass(int $classId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM trial_classes WHERE id = ?');
        $stmt->execute([$classId]);
    }
}
