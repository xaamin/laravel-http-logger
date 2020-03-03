<?php
namespace Xaamin\HttpLogger\Loggers;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Contracts\Container\Container;
use Xaamin\HttpLogger\Loggers\AbstractLogger;
use Symfony\Component\HttpFoundation\Response;
use Xaamin\HttpLogger\Jobs\LogTheHttpRequestJob;
use Xaamin\HttpLogger\Contracts\PersistentLoggerWriterInterface;

class DatabaseLogger extends AbstractLogger implements PersistentLoggerWriterInterface
{
    private $connection;
    private $container;
    private $table;

    public function __construct(ConnectionInterface $connection, $table, Container $container = null)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->container = $container;
    }

    public function log(Request $request = null, Response $response = null, array $meta = [])
    {
        $request = $request ? : request();

        $data = $this->getHeadersInfo($request)
            + $this->getRequestInfo($request)
            + $this->getUserInfo($this->container)
            + $this->getBrowserInfo($this->container)
            + $this->getResponse($response)
            + array_merge($meta, $this->getMetaFromCustomInputCreator($this->container, $request));

        $data['input'] = json_encode(Arr::get($data, 'input', []));
        $data['files'] = json_encode(Arr::get($data, 'files', []));

        return $this->queue($data, null);
    }

    public function queue(array $data, $identifier)
    {
        $queue = config('http-logger.queue');

        if ($queue) {
            $queue = $queue === true ? 'default' : $queue;

            $job = (new LogTheHttpRequestJob($data, $identifier))->onQueue($queue)->delay($identifier ? 2 : 0);

            dispatch($job);
        } else {
            if ($identifier) {
                return $this->update($data, $identifier);
            } else {
                return $this->store($data);
            }
        }
    }

    public function store(array $data)
    {
        $data['created_at'] = Carbon::now();

        return $this->getQuery()->insert($data);
    }

    public function update(array $data, $identifier)
    {
        $data['updated_at'] = Carbon::now();

        return
            $this->getQuery()
                ->where('uuid', '=', $identifier)
                ->limit(1)
                ->update($data);
    }

    /**
     * Get a fresh query builder instance for the table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getQuery()
    {
        return $this->connection->table($this->table);
    }
}
