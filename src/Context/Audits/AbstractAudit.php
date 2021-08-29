<?php namespace Celestriode\Constructure\Context\Audits;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\Constructure\Context\AuditInterface;
use Celestriode\Constructure\Structures\StructureInterface;

/**
 * A general audit base for other audits to optionally extend. Not required for audits to work, but offers some useful
 * functionality.
 *
 * Predicates are audits that are run silently (i.e. the event handler is muted during the check). Using runPredicates()
 * will return whether or not all predicates passed. You can use this to restrict an audit's ability to audit based on
 * some criteria defined by other audits.
 *
 * Also supports singleton instantiation. Use when the audit in question cannot vary from application to application.
 *
 * @package Celestriode\Constructure\Context\Audits
 */
abstract class AbstractAudit implements AuditInterface
{
    /**
     * @var array The audit instances.
     */
    private static $instances = [];

    /**
     * @var AuditInterface[] Optional audits to silently check.
     */
    private $predicates = [];

    /**
     * @var bool Whether or not to defer the audit.
     */
    private $deferred = false;

    /**
     * Marks the audit as deferred. This means it will run after all non-deferred audits, which can be useful for
     * auditing failed audits (when using AbstractStructure or having implemented such functionality in your own base).
     *
     * @param bool $defer Whether or not to defer the audit.
     * @return $this
     */
    public function defer(bool $defer = true): self
    {
        $this->deferred = $defer;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isDeferred(): bool
    {
        return $this->deferred;
    }

    /**
     * @inheritDoc
     */
    public function addPredicates(AuditInterface ...$predicates): AuditInterface
    {
        foreach ($predicates as $predicate) {

            $this->addPredicate($predicate);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addPredicate(AuditInterface $predicate): AuditInterface
    {
        $this->predicates[] = $predicate;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPredicates(): array
    {
        return $this->predicates;
    }

    /**
     * @inheritDoc
     */
    public function runPredicates(AbstractConstructure $constructure, StructureInterface $input, StructureInterface $expected): bool
    {
        // If there are no predicates, skip and return true.

        if (empty($this->getPredicates())) {

            return true;
        }

        // Mute the event handler.

        $constructure->getEventHandler()->mute();

        // Cycle through predicates.

        foreach ($this->getPredicates() as $predicate) {

            // If the predicate fails, unmute and return false.

            if (!$predicate->audit($constructure, $input, $expected)) {

                $constructure->getEventHandler()->unmute();

                return false;
            }
        }

        // No predicate failed, unmute and return true.

        $constructure->getEventHandler()->unmute();

        return true;
    }

    /**
     * Returns the name of the audit. Any other implementation is up to extending libraries.
     *
     * @return string
     */
    public function toString(): string
    {
        return static::getName();
    }

    /**
     * Creates a singleton of the audit. Useful for audits that do not take any arguments and therefore do not need more
     * than one instance to exist.
     *
     * @return static
     */
    final public static function get(): self
    {
        // Return if the singleton was already instantiated.

        if (isset(self::$instances[static::class])) {

            return self::$instances[static::class];
        }

        // If not, create, store, and return the class.

        $class = new static();
        self::$instances[static::class] = $class;

        return $class;
    }
}