<?php
namespace Xaamin\HttpLogger\Contracts;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface LoggerWriterInterface
{
    public function log(Request $request, Response $response = null, array $meta = []);
}