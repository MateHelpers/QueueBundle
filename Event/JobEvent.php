<?php


namespace Mate\QueueBundle\Event;

use Mate\QueueBundle\Console\Formatter;
use Mate\QueueBundle\Worker\Job;
use Symfony\Component\Console\Event\ConsoleCommandEvent;


class JobEvent extends ConsoleCommandEvent
{
    /** @var Job */
    protected $job;

    /** @var \Exception */
    protected $exception;

    public function setJob( Job $job )
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @param \Exception $exception
     */
    public function setException( $exception )
    {
        $this->exception = $exception;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return Job
     */
    public function getJob(): Job
    {
        return $this->job;
    }

    public function format( $type, $message, $color ): ?string
    {
        if ( $formatter = $this->getFormatter() ) {
            return $formatter->make($type, $message, $color);
        }

        return null;
    }

    public function getFormatter(): Formatter
    {
        $formatterHelper = $this->getFormatterHelper();

        return new Formatter($formatterHelper);
    }

    /**
     * @return mixed|null
     *
     * @throws \Exception
     */
    private function getFormatterHelper()
    {
        if ( $command = $this->getCommand() ) {
            return $command->getHelper('formatter');
        }

        return null;
    }


}