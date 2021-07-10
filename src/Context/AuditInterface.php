<?php namespace Celestriode\Constructure\Context;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\Constructure\Structures\StructureInterface;

/**
 * An audit takes in a structure that would be considered user input and
 * the expected structure and performs some check. If the check fails, it
 * returns false.
 *
 * @package Celestriode\Constructure\Context
 */
interface AuditInterface
{
    /**
     * Takes in the input and the expected structure and performs some check.
     *
     * @param AbstractConstructure $constructure The base Constructure object, which holds the event handler.
     * @param StructureInterface $input The input to be compared with the expected structure.
     * @param StructureInterface $expected The expected structure that the input should adhere to.
     * @return boolean
     */
    public function audit(AbstractConstructure $constructure, StructureInterface $input, StructureInterface $expected): bool;

    /**
     * Returns whether or not the audit has been deferred. Deferred audits are meant to run after other audits do. Use
     * this in the event an audit needs to run after other audits, if necessary.
     *
     * @return bool
     */
    public function isDeferred(): bool;

    /**
     * The user-friendly name for the audit, which can be displayed to the user or used when logged.
     *
     * @return string
     */
    public static function getName(): string;

}