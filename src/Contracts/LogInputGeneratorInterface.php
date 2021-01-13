<?php
namespace Xaamin\HttpLogger\Contracts;

use Illuminate\Http\Request;

interface LogInputGeneratorInterface
{
    public function generate(Request $request);
}
