<?php
namespace Xaamin\HttpLogger\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Xaamin\HttpLogger\HttpLoggerManager;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogTheHttpRequestJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    protected $identifier;

    public function __construct(array $data, $identifier)
    {
        $this->data = $data;
        $this->identifier = $identifier;
    }

    public function handle(HttpLoggerManager $logger)
    {
        if ($this->identifier) {
            $logger->update($this->data, $this->identifier);
        } else {
            $logger->store($this->data);
        }
    }
}