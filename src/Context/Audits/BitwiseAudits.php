<?php namespace Celestriode\Constructure\Context\Audits;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\Constructure\Context\AuditInterface;
use Celestriode\Constructure\Exceptions\AbstractConstructureException;
use Celestriode\Constructure\Structures\StructureInterface;

/**
 * A selection of bitwise operations to use with a set of audits. Takes in which operator to use and a list of audits.
 * For example, using the OR operator with three audits will require at least one of those audits to pass for the input.
 *
 * @package Celestriode\Constructure\Context\Audits
 */
class BitwiseAudits extends AbstractAudit
{
    public const OR_FAILED = '2d77aa14-9b33-447a-999d-56f8d4eefbde';
    public const XOR_MULTIPLE_PASS = 'ec0eeaae-abe9-4c58-8a2a-6827fb55580b';
    public const XOR_FAILED = '94ccbcd2-49c1-4fd6-8f34-79d66212acd7';
    public const NOT_FAILED = '19427129-09d1-4626-a468-96045992cc1b';

    public const OR = 1;
    public const XOR = 2;
    public const NOT = 3;

    /**
     * @var int The operator to use for this audit.
     */
    protected $operator;

    /**
     * @var AuditInterface[] The audits to use with the operator.
     */
    protected $audits;

    public function __construct(int $operator, AuditInterface ...$audits)
    {
        $this->operator = $operator;
        $this->audits = $audits;
    }

    /**
     * Determines whether the input passes a series of audits, based upon the bitwise operator.
     *
     * @param AbstractConstructure $constructure The base Constructure object, which holds the event handler.
     * @param StructureInterface $input The input to be compared with the expected structure.
     * @param StructureInterface $expected The expected structure that the input should adhere to.
     * @return boolean
     */
    public function audit(AbstractConstructure $constructure, StructureInterface $input, StructureInterface $expected): bool
    {
        switch ($this->getOperator()) {

            case self::OR:
                return $this->bitwiseOr($constructure, $input, $expected);
            case self::XOR:
                return $this->bitwiseXor($constructure, $input, $expected);
            case self::NOT:
                return $this->bitwiseNot($constructure, $input, $expected);
            default:
                throw new class('Invalid operator: ' . $this->getOperator()) extends AbstractConstructureException {};
        }
    }

    /**
     * Determines if the input passes at least one of the audits supplied to this audit.
     *
     * @param AbstractConstructure $constructure The base Constructure object, which holds the event handler.
     * @param StructureInterface $input The input to be compared with the expected structure.
     * @param StructureInterface $expected The expected structure that the input should adhere to.
     * @return bool
     */
    protected function bitwiseOr(AbstractConstructure $constructure, StructureInterface $input, StructureInterface $expected): bool
    {
        // Store captured events separately for later.

        $capturedEvents = [];

        // Cycle through all audits.

        foreach ($this->getAudits() as $audit) {

            // Clear previously-captured events and then capture.

            $constructure->getEventHandler()->capture();

            // If the audit passes, then that's all that's needed.

            if ($audit->audit($constructure, $input, $expected)) {

                // Release captured events and return true.

                $constructure->getEventHandler()->release();

                return true;
            }

            // Set aside captured events and clear them from the event handler the next cycle.

            $capturedEvents = array_merge($capturedEvents, $constructure->getEventHandler()->getCapturedEvents());
            $constructure->getEventHandler()->clear();
        }

        // None of the audits passed, run the captured events.

        foreach ($capturedEvents as $capturedEvent) {

            $constructure->getEventHandler()->runEvent($capturedEvent->getFunction(), ...$capturedEvent->getInputs());
        }

        // Trigger failure event and return false.

        $constructure->getEventHandler()->trigger(self::OR_FAILED, $capturedEvents, $this, $input, $expected);

        return false;
    }

    /**
     * Determines if the input passes only one of the supplied audits.
     *
     * @param AbstractConstructure $constructure The base Constructure object, which holds the event handler.
     * @param StructureInterface $input The input to be compared with the expected structure.
     * @param StructureInterface $expected The expected structure that the input should adhere to.
     * @return bool
     */
    protected function bitwiseXor(AbstractConstructure $constructure, StructureInterface $input, StructureInterface $expected): bool
    {
        $successfulAudit = null;
        $successfulEvents = [];
        $failedEvents = [];

        // Cycle through all audits.

        foreach ($this->getAudits() as $audit) {

            // Capture events.

            $constructure->getEventHandler()->capture();

            // If the audit passes...

            if ($audit->audit($constructure, $input, $expected)) {

                // And if a previous audit already passed, return false.

                if ($successfulAudit !== null) {

                    $constructure->getEventHandler()->trigger(self::XOR_MULTIPLE_PASS, $successfulAudit, $successfulEvents, $audit, $constructure->getEventHandler()->getCapturedEvents(), $this, $input, $expected);
                    $constructure->getEventHandler()->clear();

                    return false;
                }

                // Otherwise mark success as true.

                $successfulAudit = $audit;
                $successfulEvents = $constructure->getEventHandler()->getCapturedEvents();
            } else {

                // Audit failed, add it to failed events.

                $failedEvents = array_merge($failedEvents, $constructure->getEventHandler()->getCapturedEvents());
            }

            // Clear captured events.

            $constructure->getEventHandler()->clear();
        }

        // If there was no successful audit, run failed events and trigger XOR failure.

        if ($successfulAudit === null) {

            foreach ($failedEvents as $failedEvent) {

                $constructure->getEventHandler()->runEvent($failedEvent->getFunction(), ...$failedEvent->getInputs());
            }

            $constructure->getEventHandler()->trigger(self::XOR_FAILED, $this, $input, $expected);

            return false;
        }

        // Otherwise everything is good, run successful events and return true.

        foreach ($successfulEvents as $successfulEvent) {

            $constructure->getEventHandler()->runEvent($successfulEvent->getFunction(), ...$successfulEvent->getInputs());
        }

        return true;
    }

    /**
     * Determines if the input does not pass any of the supplied audits. That is, all supplied audits must fail.
     *
     * @param AbstractConstructure $constructure The base Constructure object, which holds the event handler.
     * @param StructureInterface $input The input to be compared with the expected structure.
     * @param StructureInterface $expected The expected structure that the input should adhere to.
     * @return bool
     */
    protected function bitwiseNot(AbstractConstructure $constructure, StructureInterface $input, StructureInterface $expected): bool
    {
        // Cycle through all audits.

        foreach ($this->getAudits() as $audit) {

            // Capture events.

            $constructure->getEventHandler()->capture();

            // If an audit passes, then this audit fails.

            if ($audit->audit($constructure, $input, $expected)) {

                // Pass the captured events alongside the NOT trigger.

                $capturedEvents = $constructure->getEventHandler()->getCapturedEvents();
                $constructure->getEventHandler()->clear();
                $constructure->getEventHandler()->trigger(self::NOT_FAILED, $audit, $capturedEvents, $this, $input, $expected);

                return false;
            }

            // Clear captured events.

            $constructure->getEventHandler()->clear();
        }

        // All audits failed, return true.

        return true;
    }

    /**
     * Returns the operator used with this audit.
     *
     * @return int
     */
    public function getOperator(): int
    {
        return $this->operator;
    }

    /**
     * Returns the audits associated with this audit.
     *
     * @return AuditInterface[]
     */
    public function getAudits(): array
    {
        return $this->audits;
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'bitwise_audits';
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        // Get audit strings.

        $auditStrings = array_map(function (AuditInterface $audit) {

            return $audit->toString();
        }, $this->getAudits());

        // Switch operator string.

        switch ($this->getOperator()) {

            case self::OR:
                $operator = 'OR';
                break;
            case self::XOR:
                $operator = 'XOR';
                break;
            case self::NOT:
                $operator = 'NOT';
                break;
            default:
                $operator = 'UNKNOWN';
        }

        // Build and return the bitwise audits string.

        return self::getName() . '{operator=' . $operator . ',audits=[' . implode(', ', $auditStrings) . ']}';
    }
}