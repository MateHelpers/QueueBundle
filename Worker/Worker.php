<?php

namespace Mate\QueueBundle\Worker;

use Pheanstalk\Pheanstalk;
use Pheanstalk\Job;

class Worker
{
    /** @var Pheanstalk */
    protected $connection;

    /**
     * Worker constructor.
     * @param string $host
     */
    public function __construct( $host = '127.0.0.1' )
    {
        $this->connection = new Pheanstalk($host);
    }

    /**
     * @param string $tube
     * @param null   $timeout
     * @return bool|object|Job
     */
    public function watch( $tube = 'application-queue', $timeout = null )
    {
        return $this->connection->watch($tube)->reserve($timeout);
    }

    /**
     * @return array
     */
    public function getTubes(): array
    {
        return $this->connection->listTubes();
    }

    /**
     * @throws \LogicException
     */
    public function denyUnlessListening(): void
    {
        if ( !$this->isListening() ) {
            throw new \LogicException('Queue server is not connected with the current configuration');
        }
    }

    /**
     * @param Job $job
     * @return Pheanstalk
     */
    public function delete( Job $job ): Pheanstalk
    {
        return $this->connection->delete($job);
    }

    /**
     * @return bool
     */
    public function isListening(): bool
    {
        return $this->connection->getConnection()->isServiceListening();
    }
    
    /**
     * @return Pheanstalk
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
