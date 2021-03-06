<?php

namespace SimpleBus\DoctrineDBALBridge\MessageBus;

use Doctrine\DBAL\Driver\Connection;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use Throwable;

class WrapsMessageHandlingInTransaction implements MessageBusMiddleware
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function handle(object $message, callable $next): void
    {
        $this->connection->beginTransaction();

        try {
            $next($message);

            $this->connection->commit();
        } catch (Throwable $error) {
            $this->connection->rollback();

            throw $error;
        }
    }
}
