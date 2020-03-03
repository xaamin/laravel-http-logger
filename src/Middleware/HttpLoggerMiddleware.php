<?php
namespace Xaamin\HttpLogger\Middleware;

use Closure;
use Webpatser\Uuid\Uuid;
use Illuminate\Http\Request;
use Xaamin\HttpLogger\HttpLoggerManager;
use Xaamin\HttpLogger\Contracts\PersistentLoggerWriterInterface;

class HttpLoggerMiddleware
{
    protected $logger;

    public function __construct(HttpLoggerManager $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $uuid = (string)Uuid::generate(4);

        $logResponsesIsAllowed = config('http-logger.responses');
        $logResponsesIsAllowed = config('http-logger.responses');
        $shouldLogRequest = $this->shouldLogRequest($request);

        if ($shouldLogRequest) {
            $data = [
                'uuid' => $uuid
            ];

            $this->logger->log($request, null, $data);
        }

        $response = $next($request);

        $end = microtime(true);

        if ($shouldLogRequest && $logResponsesIsAllowed) {
            $data = [
                'response_time' => round($end - $start, 4)
            ];

            if ($this->logger->driver() instanceof PersistentLoggerWriterInterface) {
                $data = $data
                    + $this->logger->getResponse($response);

                $this->logger->queue($data, $uuid);
            } else {
                $this->logger->log($request, $response, $data);
            }

        }

        return $response;
    }

    private function shouldLogRequest(Request $request)
    {
        $methods = config('http-logger.methods');

        $isAllMethodsAllowed = $methods === '*' || in_array('*', $methods);
        $isMethodAllowedFromRequest = in_array(strtolower($request->method()), $methods);

        return $isAllMethodsAllowed || $isMethodAllowedFromRequest;
    }
}
