<?php


namespace Mate\QueueBundle\Worker;


interface JobInterface
{
    public function handle();
}