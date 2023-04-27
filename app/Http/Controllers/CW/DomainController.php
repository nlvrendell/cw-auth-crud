<?php

namespace App\Http\Controllers\CW;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class DomainController extends Controller
{
    public $cw_base_api;

    public function __construct()
    {
        $this->cw_base_api = env('CW_BASE_API_URL');
    }

    public function index(Request $request)
    {
        $hasError = null;
        $domains = $this->domains($request);
        if ($domains->forbidden()) {
            $hasError = 'The site is forbidden';
        } elseif ($domains->unauthorized()) {
            $hasError = 'Unauthorized to access the site';
        } elseif ($domains->notFound()) {
            $hasError = 'Unable to found the site';
        } elseif ($domains->badRequest()) {
            $hasError = 'Bad Request';
        }

        return Inertia::render('Dashboard/Index', [
            'hasError' => $hasError,
            'token' => $request->session()->get('access_token'),
            'domains' => $domains->json(),
        ]);
    }

    public function domains(Request $request)
    {

        $page_start = $request->start || 0;
        $page_end = $request->limit || 9;

        // return Http::withToken('e911c0c08b7ffe5fe03d7200a419ed4c')->post($this->cw_base_api.'?action=read&object=domain&format=json', []);
        return Http::withToken($request->session()->get('access_token'))->post($this->cw_base_api.'?action=read&object=domain&format=json&start='.strval($page_start).'&?limit='.strval($page_end).'', []);
    }
}
