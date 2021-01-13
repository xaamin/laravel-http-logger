<?php
namespace Xaamin\HttpLogger\Contracts;

use Illuminate\Http\Request;

interface LogProfileInterface
{
    public function allows(Request $request);
}