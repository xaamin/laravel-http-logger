<?php
namespace Xaamin\HttpLogger;

use Illuminate\Support\Manager;
use Xaamin\HttpLogger\Loggers\DatabaseLogger;
use Xaamin\HttpLogger\Loggers\FileLogger;

class HttpLoggerManager extends Manager
{
    protected function createFileDriver()
    {
        return new FileLogger($this->app);
    }

    protected function createDatabaseDriver()
    {
        $table = $this->app['config']['http-logger.table'];

        $connection = $this->getDatabaseConnection();

        return new DatabaseLogger($connection, $table, $this->app);
    }

    protected function getDatabaseConnection()
    {
        $connection = $this->app['config']['database.default'];

        return $this->app['db']->connection($connection);
    }

    public function getDefaultDriver()
    {
        return $this->app['config']['http-logger.driver'];
    }
}