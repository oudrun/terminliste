<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\TrialRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AdminController extends Controller
{
    public function handle(Request $request): View|RedirectResponse
    {
        $repository = new TrialRepository();

        if ($request->isMethod('post')) {
            $action = (string) $request->input('action', '');

            if ($action === 'addTrial') {
                $repository->addTrial($request->all());

                return redirect()->route('admin')->with('flash', [
                    'message' => 'Prøven ble lagt til.',
                    'type' => 'success',
                ]);
            }

            if ($action === 'addClass') {
                $repository->addClass($request->all());

                return redirect()->route('admin')->with('flash', [
                    'message' => 'Klassen ble lagt til.',
                    'type' => 'success',
                ]);
            }

            if ($action === 'updateStatus') {
                $allowed = ['pending', 'confirmed', 'cancelled'];
                $status = (string) $request->input('status', 'pending');

                if (!in_array($status, $allowed, true)) {
                    $status = 'pending';
                }

                $repository->updateRegistrationStatus((int) $request->input('registration_id', 0), $status);

                return redirect()->route('admin')->with('flash', [
                    'message' => 'Påmeldingsstatus ble oppdatert.',
                    'type' => 'success',
                ]);
            }

            if ($action === 'deleteTrial') {
                $repository->deleteTrial((int) $request->input('trial_id', 0));

                return redirect()->route('admin')->with('flash', [
                    'message' => 'Prøven ble slettet.',
                    'type' => 'success',
                ]);
            }

            if ($action === 'deleteClass') {
                $repository->deleteClass((int) $request->input('class_id', 0));

                return redirect()->route('admin')->with('flash', [
                    'message' => 'Klassen ble slettet.',
                    'type' => 'success',
                ]);
            }
        }

        return view('admin', [
            'trials' => $repository->getAllTrials(),
            'classes' => $repository->getAllClasses(),
            'registrations' => $repository->getAllRegistrations(),
        ]);
    }
}
