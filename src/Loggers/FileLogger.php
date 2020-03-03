<?php
namespace Xaamin\HttpLogger\Loggers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Contracts\Container\Container;
use Xaamin\HttpLogger\Loggers\AbstractLogger;
use Symfony\Component\HttpFoundation\Response;

class FileLogger extends AbstractLogger
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function log(Request $request = null, Response $response = null, array $meta = [])
    {
        $oneTab = $this->getTabs(1);
        $twoTabs = $this->getTabs(2);

        $request = $request ? : request();

        $headers = $this->getHeadersInfo($request);
        $data = $this->getRequestInfo($request);
        $response = $this->getResponse($response);;

        $url = Arr::get($data, 'url');
        $method = Arr::get($data, 'method');
        $bodyAsJson = json_encode(Arr::get($data, 'input', []));

        $files = Arr::get($data, 'files', []);
        $user = $this->getUserInfo($this->container);
        $browser = $this->getBrowserInfo($this->container);
        $files = implode(', ', $files);

        $callback = function ($value) use ($twoTabs) {
            return "{$twoTabs}{$value}\n";
        };

        $headers = explode("\n", $headers['headers']);
        $headers = array_map($callback, $headers);
        $headers = trim(implode("", $headers), "\n");

        $browserInfo = (!empty($browser) ? "{$browser['browser']} on platform {$browser['platform']}" : "");
        $requestInfo = "\n{$method} {$url} - " . $browserInfo;
        $requestInfo .= "\n\n{$oneTab}{$bodyAsJson} \n{$oneTab}";
        $requestInfo .= "\n{$oneTab}Files: " . ($files !== '' ? : 'None');
        $headersInfo = "\n\n{$oneTab}Headers: \n{$headers}";
        $userInfo = !empty($user) ? "\n{$oneTab} User:{$user['user_id']}" : "";
        $responseInfo = $response ? "\nResponse: {$response['status_code']} - {$response['response_type']} in {$meta['response_time']} s \n{$oneTab}{$response['response_body']}" : "";

        $message = $requestInfo
            . $userInfo
            . $headersInfo
            . $responseInfo;

        info($message);
    }


    protected function getTabs($number = 1, $size = 2)
    {
        $tabs = "";

        foreach (range(1, $number) as $tab) {
            foreach (range(1, $size) as $space) {
                $tabs .= " ";
            }
        }

        return $tabs;
    }
}
