<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public $cw_base_api;

    public $client_id;

    public $client_secret;

    public function __construct()
    {
        $this->cw_base_api = config('connectware.api');
        $this->client_id = config('connectware.client_id');
        $this->client_secret = config('connectware.client_secret');
    }

    public function store(Request $request)
    {

        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $response = Http::post($this->cw_base_api.'oauth2/token', [
            'username' => $request->username,
            'password' => $request->password,
            'grant_type' => 'password',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
        ]);

        if ($response->forbidden()) {
            $this->throwError('username', 'You have inputted invalid credentials.');
        }

        if ($response->ok()) {
            $request->session()->put('access_token', $response['access_token']);

            return redirect()->route('dashboard');
        }
    }

    public function throwError($key, $message)
    {
        throw ValidationException::withMessages([
            $key => $message,
        ]);
    }
}
