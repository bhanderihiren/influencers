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

function generatePlatformLink($platform, $username)
{
    if (!$username) return '#'; // Return placeholder if username is missing

    $links = [
        'tiktok' => "https://www.tiktok.com/@$username",
        'facebook' => "https://www.facebook.com/$username",
        'instagram' => "https://www.instagram.com/$username",
        'x' => "https://twitter.com/$username",
    ];

    return $links[$platform] ?? '#'; // Default to '#' if platform is unknown
}

function validateSocialMediaUsername($platform, $username, $url)
{
    switch (strtolower($platform)) {
        case 'facebook':
            return validateFacebook($username, $url);
        case 'twitter':
            return validateTwitter($username, $url);
        case 'instagram':
            return validateInstagram($username, $url);
        default:
            return true;
    }
}

function validateFacebook($username, $url)
{
    if (!preg_match('/^[a-zA-Z0-9\.]{5,50}$/', $username)) {
        return false;
    }

    $parsed = parse_url($url);
    if (!isset($parsed['host']) || !str_contains($parsed['host'], 'facebook.com')) {
        return false;
    }
    
    return true;
}

function validateTwitter($username, $url)
{
    if (!preg_match('/^[A-Za-z0-9_]{1,15}$/', $username)) {
        return false;
    }

    $parsed = parse_url($url);
    if (!isset($parsed['host']) || 
        (!str_contains($parsed['host'], 'twitter.com') && 
         !str_contains($parsed['host'], 'x.com'))) {
        return false;
    }

    return true;
}

function validateInstagram($username, $url)
{
    if (!preg_match('/^[A-Za-z0-9_\.]{1,30}$/', $username)) {
        return false;
    }
    $parsed = parse_url($url);
    if (!isset($parsed['host']) || !str_contains($parsed['host'], 'instagram.com')) {
        return false;
    }
    return true;
}