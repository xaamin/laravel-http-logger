<?php
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

if (!function_exists('http_log')) {
    function http_log(Request $request = null, Response $response = null, array $meta = [])
    {
        app('http-logger')->log($request, $response, $meta);
    }
}
