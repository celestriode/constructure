<?php namespace Celestriode\Constructure\Reports;

use Celestriode\Constructure\Reports\Severities\SeverityInterface;
use Celestriode\Constructure\Reports\Severities\Debug;
use Celestriode\Constructure\Reports\Severities\Info;
use Celestriode\Constructure\Reports\Severities\Fatal;
use Celestriode\Constructure\Reports\Severities\Error;
use Celestriode\Constructure\Reports\Severities\Warn;

/**
 * A standard report message optionally available for all structure validators to use.
 *
 * Primarily implements simple methods, but comes with some helper methods to more easily
 * create messages based on standard severities packaged with this library.
 */
class Message implements MessageInterface
{
    /** @var string $format The format using sprintf. */
    private $format = '';

    /** @var array $args The arguments to apply to the format. */
    private $args = [];

    /** @var ContextInterface $context The context relating to the message. */
    private $context;

    /** @var SeverityInterface $severity The severity of the message. */
    private $severity;

    public function __construct(ContextInterface $context, string $format, string ...$args)
    {
        $this->setContext($context);
        $this->setFormat($format);
        $this->setArgs(...$args);
    }

    /**
     * Sets the format to be filled in with args.
     *
     * See sprintf() for formatting details.
     *
     * @param string $format The format to use.
     * @return void
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * Sets the arguments to be filled into the format.
     *
     * @param string ...$args Arguments to fill.
     * @return void
     */
    public function setArgs(string ...$args): void
    {
        $this->args = $args;
    }

    /**
     * Sets the context relevant to this report.
     *
     * Context is used in order to provide a stringified
     * version of the problematic structure.
     *
     * @param ContextInterface $context Context regarding this report.
     * @return void
     */
    public function setContext(ContextInterface $context): void
    {
        $this->context = $context;
    }

    /**
     * Sets the severity of the report, if applicable.
     *
     * @param SeverityInterface $severity The severity of the report.
     * @return void
     */
    public function setSeverity(SeverityInterface $severity): void
    {
        $this->severity = $severity;
    }

    /**
     * Combines format and args to create the final message.
     *
     * @return string
     */
    public function buildMessage(): string
    {
        return sprintf($this->getFormat(), ...$this->getArgs());
    }

    /**
     * Returns the raw format being used.
     *
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Returns the args that will be applied to the format.
     *
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * Returns the context associated with the report.
     *
     * @return ContextInterface
     */
    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    /**
     * Returns the severity of the message.
     *
     * Null if no severity whatsoever (just a simple message).
     *
     * @return SeverityInterface|null
     */
    public function getSeverity(): ?SeverityInterface
    {
        return $this->severity;
    }

    /**
     * Creates a new message with the provided data.
     *
     * @param ContextInterface $context Context regarding the message.
     * @param SeverityInterface $severity The severity of the message.
     * @param string $format The message itself using sprintf format.
     * @param string ...$args The arguments to add to the format.
     * @return self
     */
    protected static function createReport(ContextInterface $context, SeverityInterface $severity, string $format, string ...$args): self
    {
        $report = new static($context, $format, ...$args);
        $report->setSeverity($severity);

        return $report;
    }

    /**
     * Creates a report message with the "debug" severity.
     *
     * Summary usage: non-issues that the developer should be made aware of. For example, when successfully performing an operation based on a field's value.
     *
     * @param ContextInterface $context Context for the message.
     * @param string $format The format of the message (see sprintf).
     * @param string ...$args Arguments to apply to the format.
     * @return self
     */
    public static function debug(ContextInterface $context, string $format, string ...$args): self
    {
        return static::createReport($context, Debug::instance(), $format, ...$args);
    }

    /**
     * Creates a report message with the "info" severity.
     *
     * Summary usage: non-issues that the user should be made aware of. For example, use of an accepted custom value outside a list of expected values.
     *
     * @param ContextInterface $context Context for the message.
     * @param string $format The format of the message (see sprintf).
     * @param string ...$args Arguments to apply to the format.
     * @return self
     */
    public static function info(ContextInterface $context, string $format, string ...$args): self
    {
        return static::createReport($context, Info::instance(), $format, ...$args);
    }

    /**
     * Creates a report message with the "warn" severity.
     *
     * Summary usage: issues that do not prevent structural validation. For example, unexpected keys.
     *
     * @param ContextInterface $context Context for the message.
     * @param string $format The format of the message (see sprintf).
     * @param string ...$args Arguments to apply to the format.
     * @return self
     */
    public static function warn(ContextInterface $context, string $format, string ...$args): self
    {
        return static::createReport($context, Warn::instance(), $format, ...$args);
    }

    /**
     * Creates a report message with the "error" severity.
     *
     * Summary usage: issues that allow structural validation to continue to some degree. For example, a field being the wrong datatype.
     *
     * @param ContextInterface $context Context for the message.
     * @param string $format The format of the message (see sprintf).
     * @param string ...$args Arguments to apply to the format.
     * @return self
     */
    public static function error(ContextInterface $context, string $format, string ...$args): self
    {
        return static::createReport($context, Error::instance(), $format, ...$args);
    }

    /**
     * Creates a report message with the "fatal" severity.
     *
     * Summary usage: issues that prevent further structural validation. For example, syntax errors.
     *
     * @param ContextInterface $context Context for the message.
     * @param string $format The format of the message (see sprintf).
     * @param string ...$args Arguments to apply to the format.
     * @return self
     */
    public static function fatal(ContextInterface $context, string $format, string ...$args): self
    {
        return static::createReport($context, Fatal::instance(), $format, ...$args);
    }
}
