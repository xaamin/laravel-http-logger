<?php
namespace Xaamin\HttpLogger\Support;

use Illuminate\Http\Request;
use Xaamin\HttpLogger\Contracts\LogProfileInterface;

class DefaultLogProfile implements LogProfileInterface
{
    public function allows(Request $request): bool
    {
        $methods = config('http-logger.methods');

        $isAllMethodsAllowed = $methods === '*' || in_array('*', $methods);
        $isMethodAllowedFromRequest = in_array(strtolower($request->method()), $methods);

        return $isAllMethodsAllowed || $isMethodAllowedFromRequest;
    }
}
