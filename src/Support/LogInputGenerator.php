<?php
namespace Xaamin\HttpLogger\Support;

use Illuminate\Http\Request;

abstract class LogInputGenerator
{
    abstract public function generate(Request $request);
}
