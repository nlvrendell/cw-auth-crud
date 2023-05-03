<?php

namespace App\Http\Controllers\CW\Domain;

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

    public function index(Request $request, $domain)
    {

        if (! $request->page) {
            $request->merge(['page' => 1]); // default page
        }

        $hasError = null;
        $transaction = $this->users($request, $domain);
        if ($transaction->forbidden()) {
            $hasError = 'The site is forbidden';
        } elseif ($transaction->unauthorized()) {
            $hasError = 'Unauthorized to access the site';
        } elseif ($transaction->notFound()) {
            $hasError = 'Unable to found the site';
        } elseif ($transaction->badRequest()) {
            $hasError = 'Bad Request';
        }

        return Inertia::render('Dashboard/Domain/DomainUsers', [
            'hasError' => $hasError,
            'domain' => $domain,
            'users' => [
                'data' => $transaction->json(),
                'count' => $this->userCount($request, $domain)->json(),
            ],
            'filters' => (object) ['search' => $request->input('search'), 'current' => (int) $request->input('page')],
        ]);
    }

    public function users(Request $request, $domain)
    {
        $page_start = 0; // start at 0 index
        $per_page = 10; // limit with 10 data it means end at index 9

        if ($request->page > 1) {
            $page_start = ($request->page * $per_page) - $per_page;
        }

        $payload = ['user' => $request->search];

        return Http::withToken($request->session()->get('access_token'))->post($this->cw_base_api.'?action=read&object=subscriber&format=json', [
            'start' => $page_start,
            'limit' => $per_page,
            'domain' => $domain,
            ...$payload,
        ]);
    }

    public function userCount(Request $request, $domain)
    {
        return Http::withToken($request->session()->get('access_token'))->post($this->cw_base_api.'?action=count&object=subscriber&format=json&domain='.$domain.'');
    }

    public function update(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'email',
        ]);

        $transaction = $this->updateUser($request);

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

    public function updateUser(Request $request)
    {
        $payload = [
            'uid' => $request->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ];

        return Http::withToken($request->session()->get('access_token'))
            ->post($this->cw_base_api.'?action=update&object=subscriber', [
                ...$payload,
            ]);
    }

    public function throwError($key, $message)
    {
        throw ValidationException::withMessages([
            $key => $message,
        ]);
    }

    public function destroy(Request $request, $domain, $user)
    {

        $transaction = $this->removeUser($request, $user);

        if ($transaction->forbidden()) {
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

    public function removeUser(Request $request, $user)
    {
        return Http::withToken($request->session()->get('access_token'))
            ->post($this->cw_base_api.'?format=json&object=subscriber&action=delete&uid='.$user.'');
    }
}
