<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\TrialRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class RegistrationController extends Controller
{
    public function handle(Request $request): View|RedirectResponse
    {
        $classId = (int) $request->query('class_id', 0);
        $repository = new TrialRepository();
        $class = $repository->getClassWithTrial($classId);

        if ($class === null) {
            return redirect()->route('home')->with('flash', [
                'message' => 'Fant ikke valgt klasse.',
                'type' => 'error',
            ]);
        }

        $errors = [];
        $formData = [
            'owner_name' => trim((string) $request->input('owner_name', '')),
            'email' => trim((string) $request->input('email', '')),
            'phone' => trim((string) $request->input('phone', '')),
            'dog_name' => trim((string) $request->input('dog_name', '')),
            'dog_regno' => trim((string) $request->input('dog_regno', '')),
            'dog_breed' => trim((string) $request->input('dog_breed', '')),
            'dog_class' => trim((string) $request->input('dog_class', '')),
            'comment' => trim((string) $request->input('comment', '')),
        ];

        if ($request->isMethod('post')) {
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

                return redirect()->route('home')->with('flash', [
                    'message' => 'Påmeldingen er registrert og venter på bekreftelse.',
                    'type' => 'success',
                ]);
            }
        }

        $available = max(0, (int) $class['max_participants'] - (int) $class['registration_count']);

        return view('apply', [
            'class' => $class,
            'available' => $available,
            'errors' => $errors,
            'formData' => $formData,
        ]);
    }
}
