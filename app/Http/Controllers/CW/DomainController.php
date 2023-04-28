<?php

namespace App\Http\Controllers\CW;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
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

        if (! $request->page) {
            $request->merge(['page' => 1]); // default page
        }

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
            'filters' => (object) ['search' => $request->input('search'), 'current' => (int) $request->input('page')],
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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'territory' => 'required',
        ]);

        $transaction = $this->createDomain($request);

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

    public function createDomain(Request $request)
    {
        $payload = [
            'domain' => $request->name,
            'territory' => $request->territory,
            'description' => $request->description,
            'call_limit' => $request->call_limit,
            'call_limit_ext' => $request->call_limit_ext,
            'max_user' => $request->max_user,
            'max_call_queue' => $request->max_call_queue,
            'max_aa' => $request->max_aa,
            'max_conference' => $request->max_conference,
            'max_department' => $request->max_department,
            'max_site' => $request->max_site,
            'max_device' => $request->max_device,
        ];

        return Http::withToken($request->session()->get('access_token'))
            ->post($this->cw_base_api.'?action=create&object=domain', [
                ...$payload,
            ]);
    }

    public function throwError($key, $message)
    {
        throw ValidationException::withMessages([
            $key => $message,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'territory' => 'required',
        ]);

        $transaction = $this->updateDomain($request);

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

    public function updateDomain(Request $request)
    {
        $payload = [
            'domain' => $request->name,
            'territory' => $request->territory,
            'description' => $request->description,
            'call_limit' => $request->call_limit,
            'call_limit_ext' => $request->call_limit_ext,
            'max_user' => $request->max_user,
            'max_call_queue' => $request->max_call_queue,
            'max_aa' => $request->max_aa,
            'max_conference' => $request->max_conference,
            'max_department' => $request->max_department,
            'max_site' => $request->max_site,
            'max_device' => $request->max_device,
        ];

        return Http::withToken($request->session()->get('access_token'))
            ->post($this->cw_base_api.'?action=update&object=domain', [
                ...$payload,
            ]);
    }

    public function destroy(Request $request, $domain)
    {
        $transaction = $this->removeDomain($request, $domain);

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

    public function removeDomain(Request $request, $domain)
    {
        return Http::withToken($request->session()->get('access_token'))
            ->post($this->cw_base_api.'?format=json&object=domain&action=delete&domain='.$domain.'');
    }
}
