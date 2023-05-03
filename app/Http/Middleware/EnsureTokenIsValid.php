<?php

namespace App\Http\Middleware;

use App\Models\Authenticated;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = Auth::guard('connectware')->user();

        if (! $user->id) {
            return redirect()->route('login');
        }

        $expiration = Carbon::parse($user->token_expired_in);
        $expirationGracePeriod = Carbon::parse($expiration)->addMinutes(30);
        $now = now();

        if (Carbon::parse($expiration)->gt($now)) {
            return $next($request);
        } elseif (Carbon::parse($now)->gt($expiration) && $expirationGracePeriod->gte($now)) {
            // If now is greather than the expiration and within 30mins below proceed to refresh token
            return $this->refreshToken($user, $next, $request);
        } else {
            // Now is 30 mins greather than the expiration then go to login
            return redirect()->route('login');
        }
    }

    public function refreshToken($user, $next, $request)
    {
        $client_id = config('connectware.client_id');
        $client_secret = config('connectware.client_secret');
        $cw_base_api = config('connectware.api');

        $response = Http::post($cw_base_api.'oauth2/token?refresh_token='.$user->refresh_token.'&grant_type=refresh_token&client_id='.$client_id.'&client_secret='.$client_secret.'');

        if ($response->ok()) {

            session(['access_token' => $response['access_token']]);
            $this->setAuthenticated($user, $response);

            return $next($request);
        }

        return redirect()->route('login');
    }

    public function setAuthenticated($user, $response)
    {
        $newUserData = [
            'access_token' => $response['access_token'],
            'refresh_token' => $response['refresh_token'],
            'token_expired_in' => now()->addSeconds(3600), // exact time token expires
        ];

        $user = tap(Authenticated::where('id', $user->id))->update($newUserData)->first();

        // save an authenticated session data
        Auth::guard('connectware')->login($user);
    }
}
