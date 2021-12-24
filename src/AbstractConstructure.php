<?php namespace Celestriode\Constructure;

use Celestriode\Constructure\Context\AuditInterface;
use Celestriode\Constructure\Context\Events\EventHandlerInterface;
use Celestriode\Constructure\Exceptions\ConversionFailureException;
use Celestriode\Constructure\Structures\StructureInterface;

/**
 * An agnostic definition for a data structure. A separate Constructure for each type of data format
 * would be created using this library as a guideline. For example, a Constructure for JSON could be
 * created to define a traversable data structure that follows Constructure's guidelines.
 * 
 * The primary usage is to validate user input. For example, this library defines the guidelines,
 * another library defines a JSON Constructure, and then a third library defines all valid JSON
 * structures for Minecraft advancements.
 *
 * @package Celestriode\Constructure
 */
abstract class AbstractConstructure
{
    /**
     * @var EventHandlerInterface The event handler associated with this Constructure. Audits can make use of it.
     */
    private $eventHandler;

    /**
     * @var AuditInterface[] The audits that will be run for all structures that use this Constructure.
     */
    protected $globalAudits = [];

    /**
     * @param EventHandlerInterface $eventHandler The event handler to use with this Constructure.
     * @param AuditInterface ...$globalAudits The optional global audits.
     */
    public function __construct(EventHandlerInterface $eventHandler, AuditInterface ...$globalAudits)
    {
        $this->addGlobalAudits(...$globalAudits);
        $this->eventHandler = $eventHandler;
    }

    /**
     * Returns whether or not the input matches the expected structure.
     *
     * @param StructureInterface $input The input that should follow the expected structure.
     * @param StructureInterface $expected A generically-defined structure for the input to follow.
     * @return bool
     */
    function validate(StructureInterface $input, StructureInterface $expected): bool
    {
        return $expected->compare($this, $input);
    }

    /**
     * Takes in an generic (likely string) input and transforms it into a structure in some manner
     * not defined here. For example, this would take a JSON string and turn it into a tree of
     * structures that are defined for a JSON Constructure library.
     *
     * @param mixed $input
     * @return StructureInterface
     * @throws ConversionFailureException
     */
    abstract public function toStructure($input): StructureInterface;

    /**
     * Adds multiple audits that are stored in the Constructure object, with the intention that
     * all structures will use them. For example, this could be used to run an audit that verifies
     * that all fields in a JSON object are the correct data type.
     *
     * The usage of global audits is up to the structures implementing StructureInterface.compare().
     * These can be ignored if desired.
     *
     * @param AuditInterface ...$globalAudits Audits to be used with all structures.
     * @return self
     */
    public function addGlobalAudits(AuditInterface ...$globalAudits): self
    {
        foreach ($globalAudits as $globalAudit) {

            $this->addGlobalAudit($globalAudit);
        }

        return $this;
    }

    /**
     * Adds a single global audit.
     *
     * @param AuditInterface $globalAudit The audit to be used with all structures.
     * @return self
     */
    public function addGlobalAudit(AuditInterface $globalAudit): self
    {
        $this->globalAudits[] = $globalAudit;

        return $this;
    }

    /**
     * Returns all global audits added to this Constructure instance.
     *
     * @return AuditInterface[]
     */
    public function getGlobalAudits(): array
    {
        return $this->globalAudits;
    }

    /**
     * Returns the event handler for this Constructure.
     *
     * @return EventHandlerInterface
     */
    final public function getEventHandler(): EventHandlerInterface
    {
        return $this->eventHandler;
    }
}