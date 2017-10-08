<?php


namespace Mate\QueueBundle;

/**
 * Class Events
 * @package Mate\QueueBundle
 */
final class Events
{
    /**
     * @Event("Mate\QueueBundle\Event\JobEvent")
     * @var string
     */
    const MATE_QUEUE_JOB_INITIALIZED = 'mate.queue.job.initialized';

    /**
     * @Event("Mate\QueueBundle\Event\JobEvent")
     * @var string
     */
    const MATE_QUEUE_JOB_EXECUTED = 'mate.queue.job.executed';

    /**
     * @Event("Mate\QueueBundle\Event\JobEvent")
     * @var string
     */
    const MATE_QUEUE_JOB_FAILED = 'mate.queue.job.failed';

    /**
     * @Event("Mate\QueueBundle\Event\JobEvent")
     * @var string
     */
    const MATE_QUEUE_JOB_DELETED = 'mate.queue.job.deleted';
}