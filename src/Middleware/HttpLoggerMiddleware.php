<?php
namespace Xaamin\HttpLogger\Middleware;

use Closure;
use Exception;
use Throwable;
use LogicException;
use Webpatser\Uuid\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Xaamin\HttpLogger\HttpLoggerManager;
use Xaamin\HttpLogger\Support\DefaultLogProfile;
use Xaamin\HttpLogger\Contracts\LogProfileInterface;
use Xaamin\HttpLogger\Contracts\PersistentLoggerWriterInterface;

class HttpLoggerMiddleware
{
    protected $logger;
    protected $profiler;

    public function __construct(HttpLoggerManager $logger, DefaultLogProfile $profiler)
    {
        $this->logger = $logger;
        $this->profiler = $profiler;
    }

    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $uuid = (string)Uuid::generate(4);

        $logResponsesIsAllowed = config('http-logger.responses');
        $shouldLogRequest = $this->shouldLogRequest($request);

        if ($shouldLogRequest) {
            $data = [
                'uuid' => $uuid
            ];

            try {
                $this->logger->log($request, null, $data);
            } catch (Throwable $th) {
                Log::critical((string)$th);
            } catch (Exception $e) {
                Log::critical((string)$e);
            }
        }

        $response = $next($request);

        $end = microtime(true);

        if ($shouldLogRequest && $logResponsesIsAllowed) {
            $data = [
                'response_time' => round($end - $start, 4)
            ];

            try {
                if ($this->logger->driver() instanceof PersistentLoggerWriterInterface) {
                    $data = array_merge($data, $this->logger->getResponse($response));

                    $this->logger->queue($data, $uuid);
                } else {
                    $this->logger->log($request, $response, $data);
                }
            } catch (Throwable $th) {
                Log::critical((string)$th);
            } catch (Exception $e) {
                Log::critical((string)$e);
            }
        }

        return $response;
    }

    private function shouldLogRequest(Request $request)
    {
        $profiler = config('http-logger.log_profile');

        if (!!$profiler) {
            try {
                $profiler = app($profiler);
            } catch (Exception $e) {
                $profiler = null;
            } catch (Throwable $th) {
                $profiler = null;
            }

            if (!$profiler instanceof LogProfileInterface) {
                throw new LogicException('Profiler is not an implementation of LogProfileInterface');
            }
        } else {
            $profiler = $this->profiler;
        }

        return $profiler->allows($request);
    }
}
