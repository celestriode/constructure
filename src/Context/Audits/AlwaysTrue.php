<?php namespace Celestriode\Constructure\Context\Audits;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\Constructure\Structures\StructureInterface;

/**
 * An audit that simply always returns true. Useful for testing purposes.
 *
 * @package Celestriode\Constructure\Context\Audits
 */
class AlwaysTrue extends AbstractAudit
{
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
        return true;
    }

    /**
     * The user-friendly name for the audit, which can be displayed to the user or used when logged.
     *
     * @return string
     */
    public static function getName(): string
    {
        return "always_true";
    }
}