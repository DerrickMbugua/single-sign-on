<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    // redirect to login
    public function redirect(Request $request)
    {

        $request->session()->put('state', $state = Str::random(40));
        Log::info("State: " .$state);

        $query = http_build_query([
            'client_id' => '9948865f-bbf7-4f5d-9d07-2d412b9df4c3',
            'redirect_uri' => 'http://127.0.0.1:8080/callback',
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
            'code_challenge_method' => 'S256',
        ]);

        return redirect('http://127.0.0.1:8000/oauth/authorize?' . $query);
    }

    public function callback(Request $request)
    {
        $state = $request->session()->pull('state');

        throw_unless(
            strlen($state) > 0 && $state === $request->state,
            InvalidArgumentException::class
        );

        $response = Http::asForm()->post('http://127.0.0.1:8000/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => '9948865f-bbf7-4f5d-9d07-2d412b9df4c3',
            'client_secret' => '7GVcnk1mwZGF1m9iS0GR57k2PTRks9Y45cIUlbiY',
            'redirect_uri' => 'http://127.0.0.1:8080/callback',
            'code' => $request->code,
        ]);

        return $response->json();
    }
}
