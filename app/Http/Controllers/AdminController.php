<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\TrialRepository;

final class AdminController
{
    public function handle(): void
    {
        $repository = new TrialRepository();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = (string) ($_POST['action'] ?? '');

            if ($action === 'addTrial') {
                $repository->addTrial($_POST);
                flash('Prøven ble lagt til.', 'success');
                redirect('/admin.php');
            }

            if ($action === 'addClass') {
                $repository->addClass($_POST);
                flash('Klassen ble lagt til.', 'success');
                redirect('/admin.php');
            }

            if ($action === 'updateStatus') {
                $allowed = ['pending', 'confirmed', 'cancelled'];
                $status = (string) ($_POST['status'] ?? 'pending');

                if (!in_array($status, $allowed, true)) {
                    $status = 'pending';
                }

                $repository->updateRegistrationStatus((int) ($_POST['registration_id'] ?? 0), $status);
                flash('Påmeldingsstatus ble oppdatert.', 'success');
                redirect('/admin.php');
            }

            if ($action === 'deleteTrial') {
                $repository->deleteTrial((int) ($_POST['trial_id'] ?? 0));
                flash('Prøven ble slettet.', 'success');
                redirect('/admin.php');
            }

            if ($action === 'deleteClass') {
                $repository->deleteClass((int) ($_POST['class_id'] ?? 0));
                flash('Klassen ble slettet.', 'success');
                redirect('/admin.php');
            }
        }

        renderView('admin', [
            'flash' => getFlash(),
            'trials' => $repository->getAllTrials(),
            'classes' => $repository->getAllClasses(),
            'registrations' => $repository->getAllRegistrations(),
        ]);
    }
}
