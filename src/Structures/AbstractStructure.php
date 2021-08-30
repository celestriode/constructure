<?php namespace Celestriode\Constructure\Structures;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\Constructure\Context\AuditInterface;

/**
 * Implementation of StructureInterface. Provides basic functionality useful for most
 * structure definitions. Feel free to extend this or make your own.
 *
 * @package Celestriode\Constructure\Structures
 */
abstract class AbstractStructure implements StructureInterface
{
    /**
     * Set of event names related to running audits.
     */
    public const AUDITS_START = "76028523-a531-467f-968e-26dadd3d0bb0";
    public const AUDIT_PREDICATES_START = "a850b59c-2fc0-4fd3-8e55-e8fb538b1b7a";
    public const AUDIT_PREDICATES_COMPLETE = "4698da52-c6cd-4bee-b14f-c21b2e6bbfcf";
    public const AUDIT_RUNNING = "3203764b-dfeb-4831-88e0-7ccc65f3a998";
    public const AUDIT_PASSED = "003e5bbd-4c94-41bd-910d-b04f61cabbef";
    public const AUDIT_FAILED = "d793a446-3081-47e1-8523-e839ff2ea385";
    public const AUDITS_COMPLETE = "6f3d5797-22d6-42a8-bfbd-e1a1b721f959";
    public const AUDITS_DEFERRED_START = "12a174af-098f-4f28-acae-0665e67c7136";
    public const AUDITS_DEFERRED_END = "e2137261-2c60-4d5e-a033-89288476b637";

    /**
     * @var mixed The raw input for this structure, if applicable. An expected structure does not make use of any input.
     */
    protected $input;

    /**
     * @var AuditInterface[] The audits attached to this structure, if any.
     */
    private $audits = [];

    /**
     * @var AuditInterface[] The audits that failed, if any. Should only be populated on the input.
     * Do not add failed audits to the expected structure.
     */
    private $failedAudits = [];

    /**
     * @var null|bool True if the input structure passed all audits. False if not. Null if auditing has yet to occur.
     * Do not set this value on the expected structure.
     */
    private $passed;

    public function __construct($input = null)
    {
        $this->setValue($input);
    }

    /**
     * Sets whether or not the structure has passed audits. The expected structure should be setting this value on the
     * input. Do not set this on the expected structure.
     *
     * @param bool $passed True if the structure passes.
     * @return $this
     */
    public function setPassed(bool $passed = true): StructureInterface
    {
        $this->passed = $passed;

        return $this;
    }

    /**
     * Returns whether or not auditing the structure has failed. Should return null when auditing has not yet occurred.
     *
     * @return bool
     */
    public function passed(): ?bool
    {
        return $this->passed;
    }

    /**
     * Takes in another structure and compares it to this one. The comparison is done using audits.
     * The comparison is not done in reverse. Essentially, the "other" structure is the user input
     * while this structure is describes the structure that the input should follow. Apply audits to
     * this structure rather than the other structure.
     * 
     * At the most basic level, it is simply comparing two values. That makes this utterly overkill for
     * that purpose. Instead, use this for comparing nested tree structures and the like, such as JSON.
     *
     * @param AbstractConstructure $constructure The Constructure object associated with this comparison.
     * @param StructureInterface $other The other structure that should adhere to this structure's audits.
     * @return boolean Whether or not all audits succeeded.
     */
    public function compare(AbstractConstructure $constructure, StructureInterface $other): bool
    {
        // Prepare the audits. Include global audits if required.

        if ($this->useGlobalAudits()) {

            $audits = array_merge($this->getAudits(), $constructure->getGlobalAudits());
        } else {

            $audits = $this->getAudits();
        }

        // Cycle through all the audits.

        $constructure->getEventHandler()->trigger(self::AUDITS_START, $other, $this, ...$audits);
        $deferred = [];

        foreach ($audits as $audit) {

            // If the audit is to be deferred, defer it.

            if ($audit->isDeferred()) {

                $deferred[] = $audit;

                continue;
            }

            // Otherwise run the audit and add it to the list of problems if it fails.

            if (!$this->runAudit($audit, $constructure, $other)) {

                $other->addFailedAudit($audit);
            }
        }

        // Run deferred audits.

        $this->runDeferredAudits($constructure, $other, ...$deferred);

        // Fire event for finishing all audits.

        $constructure->getEventHandler()->trigger(self::AUDITS_COMPLETE, $other->getFailedAudits(), $other, $this, ...$audits);

        // Mark the other structure as having passed or not.

        $other->setPassed((count($other->getFailedAudits()) == 0));

        // Return whether or not all audits passed.

        return $other->passed();
    }

    /**
     * Returns whether or not global audits should be used when performing audits.
     *
     * @return bool
     */
    protected function useGlobalAudits(): bool
    {
        return true;
    }

    /**
     * Executes all deferred audits. Any failed audits will be added to the list of failed audits.
     *
     * @param AbstractConstructure $constructure The Constructure object associated with this comparison.
     * @param StructureInterface $other The other structure that should adhere to this structure's audits.
     * @param AuditInterface ...$deferred The deferred audits to run.
     */
    protected function runDeferredAudits(AbstractConstructure $constructure, StructureInterface $other, AuditInterface ...$deferred): void
    {
        // Fire event for starting deferred audits.

        $constructure->getEventHandler()->trigger(self::AUDITS_DEFERRED_START, $other, $this, ...$deferred);

        // Cycle through the deferred audits.

        foreach ($deferred as $audit) {

            // Run the deferred audit and add it to the list of problems if it fails.

            if (!$this->runAudit($audit, $constructure, $other)) {

                $other->addFailedAudit($audit);
            }
        }

        // Fire event for finishing deferred audits.

        $constructure->getEventHandler()->trigger(self::AUDITS_DEFERRED_END, $other, $this, ...$deferred);
    }

    /**
     * Runs the selected audit and returns whether or not the audit passes.
     *
     * @param AuditInterface $audit The audit to run.
     * @param AbstractConstructure $constructure The Constructure object associated with this comparison.
     * @param StructureInterface $other The other structure that should adhere to this structure's audits.
     * @return bool
     */
    protected function runAudit(AuditInterface $audit, AbstractConstructure $constructure, StructureInterface $other): bool
    {
        // Run predicates first, if any.

        $constructure->getEventHandler()->trigger(self::AUDIT_PREDICATES_START, $audit, $other, $this);

        $predicatesPass = $audit->runPredicates($constructure, $other, $this);

        $constructure->getEventHandler()->trigger(self::AUDIT_PREDICATES_COMPLETE, $audit, $other, $this, $predicatesPass);

        // If predicates did not pass, pretend the audit did not even exist (which means it passes).

        if (!$predicatesPass) {

            return true;
        }

        // If the audit failed, mark success as false. Continue doing audits though; no need to hide other issues.

        $constructure->getEventHandler()->trigger(self::AUDIT_RUNNING, $audit, $other, $this);

        if ($audit->audit($constructure, $other, $this)) {

            // Fire event for audit success.

            $constructure->getEventHandler()->trigger(self::AUDIT_PASSED, $audit, $other, $this);

            return true;
        }

        // Fire event for audit failure.

        $constructure->getEventHandler()->trigger(self::AUDIT_FAILED, $audit, $other, $this);

        return false;
    }

    /**
     * Replaces all audits in this context with the input.
     *
     * @param AuditInterface ...$audits The audits to set.
     * @return self
     */
    public function setAudits(AuditInterface ...$audits): StructureInterface
    {
        $this->audits = $audits;

        return $this;
    }

    /**
     * Adds multiple audits to the context.
     *
     * @param AuditInterface ...$audits The audits to add.
     * @return self
     */
    public function addAudits(AuditInterface ...$audits): StructureInterface
    {
        foreach ($audits as $audit) {

            $this->addAudit($audit);
        }

        return $this;
    }

    /**
     * Adds a single audit to the context.
     *
     * @param AuditInterface $audit The audit to add.
     * @return self
     */
    public function addAudit(AuditInterface $audit): StructureInterface
    {
        $this->audits[] = $audit;

        return $this;
    }

    /**
     * Returns all audits.
     *
     * @return AuditInterface[]
     */
    final public function getAudits(): array
    {
        return $this->audits;
    }

    /**
     * Adds an audit that failed to the list of audits that failed.
     *
     * @param AuditInterface $failedAudit The failed audit to add.
     * @return $this
     */
    public function addFailedAudit(AuditInterface $failedAudit): StructureInterface
    {
        $this->failedAudits[] = $failedAudit;

        return $this;
    }

    /**
     * Returns all the audits that failed to pass.
     *
     * @return AuditInterface[]
     */
    final public function getFailedAudits(): array
    {
        return $this->failedAudits;
    }

    /**
     * Sets the raw input of the structure that would result in this object being created.
     *
     * @param mixed $input The input, whatever it may be.
     * @return self
     */
    public function setValue($input = null): StructureInterface
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Returns the input, if existent.
     *
     * @return mixed
     */
    final public function getValue()
    {
        return $this->input;
    }
}