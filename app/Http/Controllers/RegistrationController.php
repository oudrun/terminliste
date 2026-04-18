<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\TrialRepository;

final class RegistrationController
{
    public function handle(int $classId): void
    {
        $repository = new TrialRepository();
        $class = $repository->getClassWithTrial($classId);

        if ($class === null) {
            flash('Fant ikke valgt klasse.', 'error');
            redirect('/');
        }

        $errors = [];
        $formData = [
            'owner_name' => trim((string) ($_POST['owner_name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'dog_name' => trim((string) ($_POST['dog_name'] ?? '')),
            'dog_regno' => trim((string) ($_POST['dog_regno'] ?? '')),
            'dog_breed' => trim((string) ($_POST['dog_breed'] ?? '')),
            'dog_class' => trim((string) ($_POST['dog_class'] ?? '')),
            'comment' => trim((string) ($_POST['comment'] ?? '')),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($formData['owner_name'] === '') {
                $errors[] = 'Fører eller eier må fylles ut.';
            }

            if ($formData['email'] === '' || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Gyldig e-post må fylles ut.';
            }

            if ($formData['dog_name'] === '') {
                $errors[] = 'Hundens navn må fylles ut.';
            }

            $available = (int) $class['max_participants'] - (int) $class['registration_count'];

            if ($available <= 0) {
                $errors[] = 'Denne klassen er fulltegnet.';
            }

            if ($errors === []) {
                $repository->addRegistration($classId, $formData);
                flash('Påmeldingen er registrert og venter på bekreftelse.', 'success');
                redirect('/');
            }
        }

        $available = max(0, (int) $class['max_participants'] - (int) $class['registration_count']);

        renderView('apply', [
            'class' => $class,
            'available' => $available,
            'errors' => $errors,
            'formData' => $formData,
        ]);
    }
}
