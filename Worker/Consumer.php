<?php


namespace Mate\QueueBundle\Worker;


class Consumer extends Worker
{
    public function unserializeAndExecute( $data )
    {
        /** @var Job $job */
        $job = unserialize($data, [ Job::class ]);

        return $this->execute($job);
    }

    public function execute( Job $job )
    {
        $this->denyUnlessListening();

        return $job->handle();
    }
}