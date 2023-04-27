<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TestController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Test', []);
    }

    public function testApi(Request $request)
    {
        // return $request->action;
        return config('connectware.api');
    }
}
