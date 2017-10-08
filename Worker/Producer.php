<?php

namespace Mate\QueueBundle\Worker;

use Pheanstalk\PheanstalkInterface;

class Producer extends Worker
{
    /** @var string */
    protected $tube = 'application-queue';

    /**
     * @param Job $job
     * @param int $delay
     * @param int $timeToRun
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function produce( Job $job, $delay = PheanstalkInterface::DEFAULT_DELAY, $timeToRun = PheanstalkInterface::DEFAULT_TTR )
    {
        $this->denyUnlessListening();

        $this
            ->connection
            ->useTube($job->getJobTube())
            ->put($job->getJobPayload(), PheanstalkInterface::DEFAULT_PRIORITY, $delay, $timeToRun);

        return $this;
    }
}