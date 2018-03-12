<?php


namespace Mate\QueueBundle\Worker;

use SuperClosure\Serializer;

class Consumer extends Worker
{
    public function unserializeAndExecute( $data )
    {
        $serializer = new Serializer();
        
        /** @var Job $job */
        $job = serialized->unserialize($data);

        return $this->execute($job);
    }

    public function execute( Job $job )
    {
        $this->denyUnlessListening();

        return $job->handle();
    }
}
