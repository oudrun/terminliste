<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\TrialRepository;
use Illuminate\Contracts\View\View;

final class HomeController extends Controller
{
    public function index(): View
    {
        $repository = new TrialRepository();
        $trials = $repository->getAllTrials();
        $classesByTrial = [];

        foreach ($trials as $trial) {
            $classesByTrial[(int) $trial['id']] = $repository->getClassesByTrial((int) $trial['id']);
        }

        return view('home', [
            'trials' => $trials,
            'classesByTrial' => $classesByTrial,
        ]);
    }
}
