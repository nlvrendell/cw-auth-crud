<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Request;
use Inertia\Inertia;

class TestController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Test', [

        ]);
    }
}
