<?php

namespace Mate\QueueBundle\Command;

use Mate\QueueBundle\Worker\Job;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\NullOutput;
use AppBundle\Job\MailJob;
use Mate\QueueBundle\Event\JobEvent;
use Mate\QueueBundle\Events;

class MateQueueWorkCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName( 'mate:queue:work' )
            ->setDescription( 'Run queue worker for listening to received jobs' )
            ->addOption(
                'no-debug',
                null,
                InputOption::VALUE_NONE,
                'Disable debug system'
            );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $dispatcher = $this->getContainer()->get( 'event_dispatcher' );
        $consumer   = $this->getContainer()->get( 'mate.queue.worker.consumer' );

        if ( $input->getOption( 'no-debug' ) ) {
            $output = new NullOutput();
        }

        while ( $consumer->isListening() ) {
            $initJob = $consumer->watch();

            if ( $initJob ) {
                /** @var MailJob $job */
                $job = unserialize( $initJob->getData(), [ Job::class ] );
                $job->setId( $initJob->getId() );

                $event = ( new JobEvent( $this, $input, $output ) )->setJob( $job );

                // middleware here before running job (timeout)
                $dispatcher->dispatch( Events::MATE_QUEUE_JOB_INITIALIZED, $event );

                try {
                    $consumer->execute( $job );

                    // middleware here after running job
                    $dispatcher->dispatch( Events::MATE_QUEUE_JOB_EXECUTED, $event );

                    $consumer->delete( $initJob );

                    // middleware here after deleting job
                    $dispatcher->dispatch( Events::MATE_QUEUE_JOB_DELETED, $event );

                } catch ( \Exception $exception ) {

                    $dispatcher->dispatch( Events::MATE_QUEUE_JOB_FAILED, $event );

                    $consumer->delete( $initJob );
                }
            }
        }

    }

}