<?php
namespace Xaamin\HttpLogger\Support;

use Illuminate\Http\Request;
use Xaamin\HttpLogger\Contracts\LogInputGeneratorInterface;

abstract class LogInputGenerator implements LogInputGeneratorInterface
{
    abstract public function generate(Request $request);
}
