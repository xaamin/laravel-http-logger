<?php
namespace Xaamin\HttpLogger\Loggers;

use Exception;
use Throwable;
use LogicException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Xaamin\HttpLogger\Support\Browser;
use Illuminate\Contracts\Container\Container;
use Symfony\Component\HttpFoundation\Response;
use Xaamin\HttpLogger\Contracts\LoggerWriterInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Xaamin\HttpLogger\Contracts\LogInputGeneratorInterface;

abstract class AbstractLogger implements LoggerWriterInterface
{
    public function getMetaFromCustomInputCreator(Request $request)
    {
        $meta = [];
        $generator = config('http-logger.generator');

        if (!!$generator) {
            try {
                $generator = app($generator);
            } catch (Exception $e) {
                $generator = null;
            } catch (Throwable $th) {
                $generator = null;
            }

            if (!$generator instanceof LogInputGeneratorInterface) {
                throw new LogicException('Profiler is not an implementation of LogInputGeneratorInterface');
            }

            $meta = $generator->generate($request);
        }

        return $meta;
    }

    /**
     * Get the basic request info
     *
     * @param Request $request
     * @return array
     */
    public function getRequestInfo(Request $request)
    {
        $except = config('http-logger.except') ? : [];

        $method = strtoupper($request->getMethod());
        $url = str_replace(url(''), '', $request->fullUrl());
        $input = $request->except($except);
        $ip_address = $request->header('X_FORWARDED_FOR', $request->ip());

        $files = iterator_to_array($request->files);

        $parseFileNames = function (UploadedFile $file) {
            return $file->getClientOriginalName();
        };

        $files = array_map($parseFileNames, $files);

        return compact('method', 'url', 'input', 'files', 'ip_address');
    }

    /**
     * Get the headers
     *
     * @param Request $request
     * @return array
     */
    public function getHeadersInfo(Request $request)
    {
        $only = config('http-logger.headers.only');

        if (!!$only) {
            $only = array_map(function ($value) {
                return Str::lower($value);
            }, $only);
        }

        $except = config('http-logger.headers.except');

        if (!!$except) {
            $except = array_map(function ($value) {
                return Str::lower($value);
            }, $except);
        }

        $headers = [];

        if ($only || $except) {
            foreach ($request->header() as $key => $value) {
                $name = Str::lower($key);

                if (
                    (!!$only && !in_array($name, (array)$only))
                    || (!$only && $except && in_array($name, (array)$except))
                ) {
                    continue;
                }

                $value = implode(';', $value);

                $headers[$key] = $value;;
            }
        }

        $headers = json_encode($headers);

        return compact('headers');
    }

    /**
     * Get logged in user info
     *
     * @param Container $container
     * @return array
     */
    public function getUserInfo()
    {
        $data = [];

        if (app()->bound(Guard::class)) {
            $auth = app('auth');
            $api = $auth->guard('api');

            if (!$auth->guest() || !$api->guest()) {
                $user = $auth->id() ? $auth : $api;

                $data['user_id'] = $user->id();
            }
        }

        return $data;
    }

    /**
     * Get browser info
     *
     * @param Container $container
     * @return array
     */
    public function getBrowserInfo()
    {
        $data = [];

        if (app()->bound('request')) {
            $request = app('request');
            $browser = new Browser($request->header('User-Agent'));

            $data['platform'] = $browser->getPlatform();
            $data['browser'] = $browser->getBrowser();

            $data['user_agent'] = substr(
                (string)$request->header('User-Agent'), 0, 500
            );
        }

        return $data;
    }

    public function getResponse(Response $response = null)
    {
        $data = [];

        if ($response) {
            $content = $response->getContent();
            $status = $response->getStatusCode();
            $type = $response->headers->get('Content-Type');

            if (Str::contains($type, 'json')) {
                $parsed = json_decode($content, true);
                $except = config('http-logger.except') ? : [];

                if ($parsed) {
                    $content = json_encode(Arr::except($parsed, $except));
                }
            }

            $data = [
                'status_code' => $status,
                'response_type' => $type,
                'response_body' => $content
            ];
        }

        return $data;
    }
}
