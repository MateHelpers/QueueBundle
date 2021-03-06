<?php


namespace Mate\QueueBundle\Worker;

use SuperClosure\Serializer;

abstract class Job implements JobInterface
{
    protected $id;

    /**
     * @return string
     */
    public function getJobName(): string
    {
        return static::class;
    }

    public function getJobPayload()
    {
        $serializer = new Serializer();
        
        return $serializer->serialize($this);
    }

    public function getJobTube()
    {
        return 'application-queue';
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    abstract public function handle();
}
