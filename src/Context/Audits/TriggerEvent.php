<?php namespace Celestriode\Constructure\Context\Audits;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\Constructure\Structures\StructureInterface;

/**
 * An audit that simply triggers an event. Useful for honing in on particular sections of structures to extract data.
 *
 * This audit is deferred, meaning it will run after non-deferred audits.
 *
 * Use predicates to prevent firing the event unless certain conditions pass.
 *
 * @package Celestriode\Constructure\Context\Audits
 */
class TriggerEvent extends AbstractAudit
{
    /**
     * @var string The name of the event to fire.
     */
    private $eventName;

    /**
     * @param string $eventName The name of the event to fire.
     */
    public function __construct(string $eventName)
    {
        $this->eventName = $eventName;
    }

    /**
     * Returns the name of the event that will be triggered.
     *
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * Takes in the input and the expected structure and performs some check.
     *
     * @param AbstractConstructure $constructure The base Constructure object, which holds the event handler.
     * @param StructureInterface $input The input to be compared with the expected structure.
     * @param StructureInterface $expected The expected structure that the input should adhere to.
     * @return boolean
     */
    public function audit(AbstractConstructure $constructure, StructureInterface $input, StructureInterface $expected): bool
    {
        // Fire the event.

        $constructure->getEventHandler()->trigger($this->getEventName(), $this, $constructure, $input, $expected);

        return true;
    }

    /**
     * Returns whether or not the audit has been deferred. Deferred audits are meant to run after other audits do. Use
     * this in the event an audit needs to run after other audits, if necessary.
     *
     * @return bool
     */
    public function isDeferred(): bool
    {
        return true;
    }

    /**
     * The user-friendly name for the audit, which can be displayed to the user or used when logged.
     *
     * @return string
     */
    public static function getName(): string
    {
        return "trigger_event";
    }
}