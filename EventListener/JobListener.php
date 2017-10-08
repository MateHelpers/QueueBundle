<?php


namespace Mate\QueueBundle\EventListener;


use Mate\QueueBundle\Event\JobEvent;
use Mate\QueueBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JobListener implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::MATE_QUEUE_JOB_INITIALIZED => 'onInitialized',
            Events::MATE_QUEUE_JOB_EXECUTED    => 'onExecuted',
            Events::MATE_QUEUE_JOB_FAILED      => 'onFailed',
            Events::MATE_QUEUE_JOB_DELETED     => 'onDeleted'
        ];
    }

    public function onInitialized( JobEvent $event )
    {
        $output  = $event->getOutput();
        $message = sprintf('%s', $event->getJob()->getJobName());

        $output->writeln($event->getFormatter()->makeProcessingMessage($message));
    }

    public function onExecuted( JobEvent $event )
    {
        $output  = $event->getOutput();
        $message = sprintf('%s', $event->getJob()->getJobName());

        $output->writeln($event->getFormatter()->makeProcessedMessage($message));
    }

    public function onFailed( JobEvent $event )
    {
        $output  = $event->getOutput();
        $message = sprintf('%s', $event->getJob()->getJobName());

        $output->writeln($event->getFormatter()->makeErrorMessage($message));
    }

    public function onDeleted( JobEvent $event )
    {
        //
    }
}