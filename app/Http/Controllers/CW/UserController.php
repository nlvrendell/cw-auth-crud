<?php

namespace App\Http\Controllers\CW;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Dashboard/Users', [
            'hasError' => null,
            'users' => [
                'data' => [],
                'count' => 0,
            ],

        ]);
    }
}
