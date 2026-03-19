<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        return Inertia::render('Dashboard/Index', [
            'stats' => $this->getStats($user),
        ]);
    }

    private function getStats($user): array
    {
        return [
            'role' => $user->role,
            'welcome_message' => 'Bem-vindo ao Painel, ' . $user->name . '!',
        ];
    }
}
