<?php
namespace Xaamin\HttpLogger\Contracts;

interface PersistentLoggerWriterInterface
{
    public function store(array $data);

    public function update(array $data, $identifier);

    public function queue(array $data, $identifier);
}