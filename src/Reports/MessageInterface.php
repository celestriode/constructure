<?php namespace Celestriode\Constructure\Reports;

use Celestriode\Constructure\Reports\Severities\SeverityInterface;

interface MessageInterface
{
    /**
     * Sets the format to be filled in with args.
     *
     * See sprintf() for formatting details.
     *
     * @param string $format The format to use.
     * @return void
     */
    public function setFormat(string $format): void;

    /**
     * Sets the arguments to be filled into the format.
     *
     * @param string ...$args Arguments to fill.
     * @return void
     */
    public function setArgs(string ...$args): void;

    /**
     * Sets the context relevant to this report.
     *
     * Context is used in order to provide a stringified
     * version of the problematic structure.
     *
     * @param ContextInterface $context Context regarding this report.
     * @return void
     */
    public function setContext(ContextInterface $context): void;

    /**
     * Sets the severity of the report, if applicable.
     *
     * @param SeverityInterface $severity The severity of the report.
     * @return void
     */
    public function setSeverity(SeverityInterface $severity): void;

    /**
     * Combines format and args to create the final message.
     *
     * @return string
     */
    public function buildMessage(): string;

    /**
     * Returns the raw format being used.
     *
     * @return string
     */
    public function getFormat(): string;

    /**
     * Returns the args that will be applied to the format.
     *
     * @return array
     */
    public function getArgs(): array;

    /**
     * Returns the context associated with the report.
     *
     * @return ContextInterface
     */
    public function getContext(): ContextInterface;

    /**
     * Returns the severity of the message.
     *
     * Null if no severity whatsoever (just a simple message).
     *
     * @return SeverityInterface|null
     */
    public function getSeverity(): ?SeverityInterface;
}
