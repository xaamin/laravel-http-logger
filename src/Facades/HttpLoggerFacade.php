<?php
namespace Xaamin\HttpLogger\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string log(Request $request)
 */
class HttpLoggerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'http-logger';
    }
}
