<?php


namespace Mate\QueueBundle\Console;


use Symfony\Component\Console\Helper\FormatterHelper;

class Formatter
{
    const PROCESSING = 'Processing';

    const PROCESSED = 'Processed ';

    const ERROR = 'Error     ';

    const GREEN_COLOR   = 'info';
    const WARNING_COLOR = 'comment';
    const ERROR_COLOR   = 'error';

    /** @var FormatterHelper */
    protected $formatterHelper;

    public function __construct( FormatterHelper $formatterHelper )
    {
        $this->formatterHelper = $formatterHelper;
    }

    public function makeProcessingMessage( $message ): string
    {
        return $this->make(self::PROCESSING, $message, self::WARNING_COLOR);
    }

    public function makeProcessedMessage( $message )
    {
        return $this->make(self::PROCESSED, $message, self::GREEN_COLOR);
    }

    public function makeErrorMessage( $message ): string
    {
        return $this->make(self::ERROR, $message, self::ERROR_COLOR);
    }

    public function make( $type, $message, $color ): string
    {
        $prefix = $this->getDateTime();

        $type    = sprintf('<%s>%s</>', $color, $type);
        $message = sprintf('%s: %s', $type, $message);

        return $this->formatterHelper->formatSection($prefix, $message, $color);
    }

    public function getPrefix( $prefix, $postfix, $color ): string
    {
        $postfix = sprintf('%s', $postfix);

        return $this->formatterHelper->formatSection($prefix, $postfix, $color);
    }

    private function getDateTime(): string
    {
        return ( new \DateTime() )->format('Y-m-d H:i:s');
    }
}