<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\TrialRepository;

final class HomeController
{
    public function index(): void
    {
        $repository = new TrialRepository();
        $trials = $repository->getAllTrials();
        $classesByTrial = [];

        foreach ($trials as $trial) {
            $classesByTrial[(int) $trial['id']] = $repository->getClassesByTrial((int) $trial['id']);
        }

        renderView('home', [
            'flash' => getFlash(),
            'trials' => $trials,
            'classesByTrial' => $classesByTrial,
        ]);
    }
}
