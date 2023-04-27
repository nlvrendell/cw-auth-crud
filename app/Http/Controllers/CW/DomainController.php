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
            'domains' => [
                'data' => $domains->json(),
                'count' => $this->domaintCount($request)->json(),
            ],
            'filters' => (object) ['search' => $request->input('search'), 'current' => $request->input('page')],
        ]);
    }

    public function domains(Request $request)
    {
        $page_start = 0; // start at 0 index
        $page_end = 10; // limit with 10 data it means end at index 9

        if ($request->page > 1) {
            $page_end = ($request->page * 10) - 1;
            $page_start = $page_end - 9;
        }

        $payload = ['domain' => $request->search];

        return Http::withToken($request->session()->get('access_token'))->post($this->cw_base_api.'?action=read&object=domain&format=json', [
            'start' => $page_start,
            'limit' => $page_end,
            ...$payload,
        ]);
    }

    public function domaintCount(Request $request)
    {
        return Http::withToken($request->session()->get('access_token'))->post($this->cw_base_api.'?action=count&object=domain&format=json');
    }
}
