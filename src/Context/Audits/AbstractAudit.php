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
     * Returns whether or not the audit has been deferred. Deferred audits are meant to run after other audits do. Use
     * this in the event an audit needs to run after other audits, if necessary.
     *
     * @return bool
     */
    public function isDeferred(): bool
    {
        return $this->deferred;
    }

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
     * Adds multiple predicates to the audit.
     *
     * @param AuditInterface ...$predicates The optional audits to silently check before firing the event.
     */
    public function addPredicates(AuditInterface ...$predicates): self
    {
        foreach ($predicates as $predicate) {

            $this->addPredicate($predicate);
        }

        return $this;
    }

    /**
     * Adds a single predicate to the audit.
     *
     * @param AuditInterface $predicate The predicate to add to the audit.
     * @return $this
     */
    public function addPredicate(AuditInterface $predicate): self
    {
        $this->predicates[] = $predicate;

        return $this;
    }

    /**
     * Returns the predicates for this audit, if any.
     *
     * @return AuditInterface[]
     */
    public function getPredicates(): array
    {
        return $this->predicates;
    }

    /**
     * Runs all the predicates (silent audits) attached to the audit. Returns whether or not they all pass.
     *
     * Use this when you want to check attached predicates.
     *
     * @param AbstractConstructure $constructure
     * @param StructureInterface $input
     * @param StructureInterface $expected
     * @return bool
     */
    protected function runPredicates(AbstractConstructure $constructure, StructureInterface $input, StructureInterface $expected): bool
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