<?php namespace Celestriode\Constructure\Exceptions;

use Celestriode\Constructure\Reports\MessageInterface;

/**
 * Parent class for all exceptions that make use of a report message.
 *
 * These exceptions can only be created by using the ::create() method, which requires a report message.
 */
abstract class AbstractReportException extends AbstractConstructureException
{
    /** @var MessageInterface $reportMessage The message thrown with the exception. */
    private $reportMessage;

    /**
     * Prevent creation without using ::create().
     */
    private function __construct()
    {
    }

    /**
     * Prevent creation without using ::create().
     *
     * @return void
     */
    private function clone()
    {
    }

    /**
     * Creates a new instance of the class alongside the report message.
     *
     * @param MessageInterface $reportMessage The message to throw with the exception.
     * @return self
     */
    final public static function create(MessageInterface $reportMessage): self
    {
        // If calling create() directly, throw error.

        if (static::class == self::class) {
            throw new \RuntimeException('Cannot create an abstract class');
        }

        // Create new static object and set its report message.

        $exc = new static();
        $exc->setReportMessage($reportMessage);

        // Return the new exception.
        
        return $exc;
    }

    /**
     * Sets the report message of this exception, to later be added to reports or however
     * the developer handles exceptions in their structure.
     *
     * @param MessageInterface $reportMessage The message thrown with the error.
     * @return void
     */
    final public function setReportMessage(MessageInterface $reportMessage): void
    {
        $this->reportMessage = $reportMessage;
    }

    /**
     * Returns the report message. There must be one.
     *
     * @return MessageInterface
     */
    final public function getReportMessage(): MessageInterface
    {
        return $this->reportMessage;
    }
}
