<?php

namespace App\Http\Controllers\CW\Domain;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Domain/DomainUsers', [
            // 'hasError' => $hasError,
            // 'users' => [
            //     'data' => $transaction->json(),
            //     'count' => $this->userCount($request)->json(),
            // ],
            // 'filters' => (object) ['search' => $request->input('search'), 'current' => (int) $request->input('page')],
        ]);
    }
}
