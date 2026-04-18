<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

final class TrialRepository
{
    public function getAllTrials(): array
    {
        return DB::table('dog_trials')->orderBy('date_start')->get()->map(fn ($row) => (array) $row)->all();
    }

    public function getClassesByTrial(int $trialId): array
    {
        return DB::table('trial_classes as c')
            ->leftJoin('registrations as r', function ($join): void {
                $join->on('r.class_id', '=', 'c.id')->where('r.status', '<>', 'cancelled');
            })
            ->select('c.*', DB::raw('COUNT(r.id) as registration_count'))
            ->where('c.trial_id', $trialId)
            ->groupBy('c.id', 'c.trial_id', 'c.name', 'c.start_time', 'c.judge', 'c.price', 'c.max_participants', 'c.notes', 'c.created_at', 'c.updated_at')
            ->orderBy('c.start_time')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    public function getClassWithTrial(int $classId): ?array
    {
        $row = DB::table('trial_classes as c')
            ->join('dog_trials as t', 't.id', '=', 'c.trial_id')
            ->leftJoin('registrations as r', function ($join): void {
                $join->on('r.class_id', '=', 'c.id')->where('r.status', '<>', 'cancelled');
            })
            ->select('c.*', 't.title', 't.organizer', 't.location', 't.trial_place', 't.trial_number', 't.contact_person', 't.contact_phone', 't.contact_email', 't.responsible_club', 't.date_start', 't.date_end', 't.registration_deadline', DB::raw('COUNT(r.id) as registration_count'))
            ->where('c.id', $classId)
            ->groupBy('c.id', 'c.trial_id', 'c.name', 'c.start_time', 'c.judge', 'c.price', 'c.max_participants', 'c.notes', 'c.created_at', 'c.updated_at', 't.id', 't.title', 't.organizer', 't.location', 't.trial_place', 't.trial_number', 't.contact_person', 't.contact_phone', 't.contact_email', 't.responsible_club', 't.date_start', 't.date_end', 't.registration_deadline')
            ->first();

        return $row ? (array) $row : null;
    }

    public function getAllClasses(): array
    {
        return DB::table('trial_classes as c')
            ->join('dog_trials as t', 't.id', '=', 'c.trial_id')
            ->select('c.*', 't.title as trial_title')
            ->orderBy('t.date_start')
            ->orderBy('c.start_time')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    public function getAllRegistrations(): array
    {
        return DB::table('registrations as r')
            ->join('trial_classes as c', 'c.id', '=', 'r.class_id')
            ->join('dog_trials as t', 't.id', '=', 'c.trial_id')
            ->select('r.*', 'c.name as class_name', 't.title as trial_title')
            ->orderByDesc('r.created_at')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    public function addTrial(array $data): void
    {
        DB::table('dog_trials')->insert([
            'trial_number' => trim((string) ($data['trial_number'] ?? '')),
            'title' => trim((string) ($data['title'] ?? '')),
            'organizer' => trim((string) ($data['organizer'] ?? '')),
            'location' => trim((string) ($data['location'] ?? '')),
            'trial_place' => trim((string) ($data['trial_place'] ?? '')),
            'date_start' => (string) ($data['date_start'] ?? ''),
            'date_end' => (string) ($data['date_end'] ?? ''),
            'registration_deadline' => (string) ($data['registration_deadline'] ?? ''),
            'description' => trim((string) ($data['description'] ?? '')),
            'contact_person' => trim((string) ($data['contact_person'] ?? '')),
            'contact_phone' => trim((string) ($data['contact_phone'] ?? '')),
            'contact_email' => trim((string) ($data['contact_email'] ?? '')),
            'responsible_club' => trim((string) ($data['responsible_club'] ?? '')),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function addClass(array $data): void
    {
        DB::table('trial_classes')->insert([
            'trial_id' => (int) ($data['trial_id'] ?? 0),
            'name' => trim((string) ($data['name'] ?? '')),
            'start_time' => ($data['start_time'] ?? '') ?: null,
            'judge' => trim((string) ($data['judge'] ?? '')),
            'price' => (float) ($data['price'] ?? 0),
            'max_participants' => (int) ($data['max_participants'] ?? 10),
            'notes' => trim((string) ($data['notes'] ?? '')),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function addRegistration(int $classId, array $data): void
    {
        DB::table('registrations')->insert([
            'class_id' => $classId,
            'owner_name' => trim((string) ($data['owner_name'] ?? '')),
            'email' => trim((string) ($data['email'] ?? '')),
            'phone' => trim((string) ($data['phone'] ?? '')),
            'dog_name' => trim((string) ($data['dog_name'] ?? '')),
            'dog_regno' => trim((string) ($data['dog_regno'] ?? '')),
            'dog_breed' => trim((string) ($data['dog_breed'] ?? '')),
            'dog_class' => trim((string) ($data['dog_class'] ?? '')),
            'comment' => trim((string) ($data['comment'] ?? '')),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function updateRegistrationStatus(int $registrationId, string $status): void
    {
        DB::table('registrations')->where('id', $registrationId)->update([
            'status' => $status,
            'updated_at' => now(),
        ]);
    }

    public function deleteTrial(int $trialId): void
    {
        DB::table('dog_trials')->where('id', $trialId)->delete();
    }

    public function deleteClass(int $classId): void
    {
        DB::table('trial_classes')->where('id', $classId)->delete();
    }
}
