<?php

namespace App\Http\Controllers\CW;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class UserController extends Controller
{
    public $cw_base_api;

    public function __construct()
    {
        $this->cw_base_api = env('CW_BASE_API_URL');
    }

    public function index(Request $request)
    {

        if (! $request->page) {
            $request->merge(['page' => 1]); // default page
        }

        $hasError = null;
        $transaction = $this->users($request);
        if ($transaction->forbidden()) {
            $hasError = 'The site is forbidden';
        } elseif ($transaction->unauthorized()) {
            $hasError = 'Unauthorized to access the site';
        } elseif ($transaction->notFound()) {
            $hasError = 'Unable to found the site';
        } elseif ($transaction->badRequest()) {
            $hasError = 'Bad Request';
        }

        return Inertia::render('Dashboard/Users', [
            'hasError' => $hasError,
            'users' => [
                'data' => $transaction->json(),
                'count' => $this->userCount($request)->json(),
            ],
            'filters' => (object) ['search' => $request->input('search'), 'current' => (int) $request->input('page')],
        ]);
    }

    public function users(Request $request)
    {
        $page_start = 0; // start at 0 index
        $page_end = 10; // limit with 10 data it means end at index 9

        if ($request->page > 1) {
            $page_end = ($request->page * 10) - 1;
            $page_start = $page_end - 9;
        }

        $payload = ['uid' => $request->search];

        return Http::withToken($request->session()->get('access_token'))->post($this->cw_base_api.'?action=read&object=subscriber&format=json', [
            'start' => $page_start,
            'limit' => $page_end,
            ...$payload,
        ]);
    }

    public function userCount(Request $request)
    {
        return Http::withToken($request->session()->get('access_token'))->post($this->cw_base_api.'?action=count&object=subscriber&format=json');
    }

    public function store(Request $request)
    {
        $request->validate([
            'domain' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'user' => 'required',
        ]);

        $transaction = $this->createUser($request);

        if ($transaction->conflict()) {
            $this->throwError('name', 'Domain already existed.');
        } elseif ($transaction->forbidden()) {
            $this->throwError('server', 'The site is forbidden');
        } elseif ($transaction->unauthorized()) {
            $this->throwError('server', 'Unauthorized to access the site');
        } elseif ($transaction->notFound()) {
            $this->throwError('server', 'Unable to found the site');
        } elseif ($transaction->badRequest()) {
            $this->throwError('server', 'Bad Request');
        }

        return redirect()->back();
    }

    public function createUser(Request $request)
    {
        $payload = [
            'action' => 'create',
            'object' => 'subscriber',
            'scope' => 'Basic User',
            'domain' => $request->name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user' => $request->user,
            'email' => $request->email,
        ];

        return Http::withToken($request->session()->get('access_token'))
            ->post($this->cw_base_api.'?object=subscriber&action=create', [
                'scope' => 'Basic User',
                'domain' => $request->domain,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'user' => $request->user,
                'email' => $request->email,
            ]);
    }

    public function throwError($key, $message)
    {
        throw ValidationException::withMessages([
            $key => $message,
        ]);
    }
}
