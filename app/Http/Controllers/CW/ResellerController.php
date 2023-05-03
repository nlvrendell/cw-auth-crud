<?php

namespace App\Http\Controllers\CW;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class ResellerController extends Controller
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
        $transaction = $this->resellers($request);
        if ($transaction->forbidden()) {
            $hasError = 'The site is forbidden';
        } elseif ($transaction->unauthorized()) {
            $hasError = 'Unauthorized to access the site';
        } elseif ($transaction->notFound()) {
            $hasError = 'Unable to found the site';
        } elseif ($transaction->badRequest()) {
            $hasError = 'Bad Request';
        }

        return Inertia::render('Dashboard/Reseller/Index', [
            'resellers' => [
                'data' => $transaction->json(),
                'count' => $this->resellerCount($request)->json(),
            ],
            'hasError' => $hasError,
            'filters' => (object) ['search' => $request->input('search'), 'current' => (int) $request->input('page')],
        ]);
    }

    public function resellers(Request $request)
    {
        $page_start = 0; // start at 0 index
        $per_page = 10; // limit with 10 data it means end at index 9

        if ($request->page > 1) {
            $page_start = ($request->page * $per_page) - $per_page;
        }

        $payload = ['territory' => $request->search];

        return Http::withToken($request->session()->get('access_token'))->post($this->cw_base_api.'?action=read&object=reseller&format=json', [
            'start' => $page_start,
            'limit' => $per_page,
            ...$payload,
        ]);
    }

    public function resellerCount(Request $request)
    {
        return Http::withToken($request->session()->get('access_token'))->post($this->cw_base_api.'?action=count&object=reseller&format=json');
    }

    public function store(Request $request)
    {
        $request->validate([
            'territory' => 'required',
            'description' => 'required',
        ]);

        $transaction = $this->createReseller($request);

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

    public function createReseller(Request $request)
    {

        return Http::withToken($request->session()->get('access_token'))
            ->post($this->cw_base_api.'?object=reseller&action=create', [
                'territory' => $request->territory,
                'description' => $request->description,
            ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'territory' => 'required',
            'description' => 'required',
        ]);

        $transaction = $this->updateReseller($request);

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

    public function updateReseller(Request $request)
    {
        $payload = [
            'territory_id' => $request->territory_id,
            'territory' => $request->territory,
            'description' => $request->description,
        ];

        return Http::withToken($request->session()->get('access_token'))
            ->post($this->cw_base_api.'?action=update&object=reseller', [
                ...$payload,
            ]);
    }

    public function destroy(Request $request, $reseller)
    {

        $transaction = $this->removeReseller($request, $reseller);

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

    public function removeReseller(Request $request, $reseller)
    {
        return Http::withToken($request->session()->get('access_token'))
            ->post($this->cw_base_api.'?format=json&object=reseller&action=delete&territory='.$reseller.'');
    }
}
