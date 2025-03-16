<?php

use Illuminate\Support\Facades\Http;

function verifyAccount($platform, $username) {
        switch (strtolower($platform)) {
            case 'instagram':
                $url = "https://www.instagram.com/$username/?__a=1";
                break;

            case 'facebook':
                $accessToken = env('FACEBOOK_ACCESS_TOKEN');
                $url = "https://graph.facebook.com/v18.0/$username?access_token=$accessToken";
                break;

            case 'twitter':
                $bearerToken = env('TWITTER_BEARER_TOKEN');
                $url = "https://api.twitter.com/2/users/by/username/$username";
                $headers = ['Authorization' => "Bearer $bearerToken"];
                break;

            case 'tiktok':
                $url = "https://www.tiktok.com/@$username";
                break;

            default:
                return response()->json(['error' => 'Unsupported platform'], 400);
        }

        // Handle platforms with authentication headers
        $response = isset($headers) ? Http::withHeaders($headers)->get($url) : Http::get($url);

        if ($response->successful()) {
            return response()->json(['exists' => true, 'platform' => $platform, 'username' => $username]);
        }

        return response()->json(['exists' => false, 'platform' => $platform, 'username' => $username], 404);
}

function assets($path){
    return asset('public/assests/' . $path) . '?ts=' . time();
}

if (!function_exists('isActiveRoute')) {
    function isActiveRoute($route, $output = 'active') {
        if (Route::currentRouteName() == $route) {
            return $output;
        }
    }
}
