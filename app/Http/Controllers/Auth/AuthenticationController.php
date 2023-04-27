<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Authenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

            $this->setAuthenticated($request, $response);

            // return Auth::guard('connectware')->user();

            return redirect()->route('dashboard', ['page' => 1]);
        }
    }

    public function throwError($key, $message)
    {
        throw ValidationException::withMessages([
            $key => $message,
        ]);
    }

    public function setAuthenticated($request, $response)
    {
        $newUserData = [
            'uid' => $response['uid'],
            'name' => $response['displayName'],
            'username' => $response['username'],
            'user' => $response['user'],
            'territory' => $response['territory'],
            'domain' => $response['domain'],
            'department' => $response['department'],
            'login' => $response['login'],
            'scope' => $response['scope'],
            'user_email' => $response['user_email'],
            'expires_in' => $response['expires_in'],
            'token_type' => $response['token_type'],
            'access_token' => $response['access_token'],
            'refresh_token' => $response['refresh_token'],
            'client_id' => $response['client_id'],
            'apiversion' => $response['apiversion'],
            'api_password' => $request->password,
        ];

        $user = Authenticated::create($newUserData);

        // save an authenticated session data
        Auth::guard('connectware')->login($user);
    }
}
