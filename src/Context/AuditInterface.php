<?php namespace Celestriode\Constructure\Context;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\Constructure\Context\Audits\AbstractAudit;
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
     * Adds multiple predicates to the audit.
     *
     * @param AuditInterface ...$predicates The optional audits to silently check before firing the event.
     * @return AuditInterface
     */
    public function addPredicates(AuditInterface ...$predicates): self;

    /**
     * Adds a single predicate to the audit.
     *
     * @param AuditInterface $predicate The predicate to add to the audit.
     * @return AuditInterface
     */
    public function addPredicate(AuditInterface $predicate): self;

    /**
     * Returns the predicates for this audit, if any.
     *
     * @return AuditInterface[]
     */
    public function getPredicates(): array;

    /**
     * Runs all the predicates (silent audits) attached to the audit. Returns whether or not they all pass.
     *
     * Use this when you want to check attached predicates. If an audit does not pass its predicates, that means the
     * audit actually passes rather than fails. This allows you to have audits that only audit under certain conditions.
     * Otherwise it is as if that audit does not exist at all.
     *
     * @param AbstractConstructure $constructure The base Constructure object, which holds the event handler.
     * @param StructureInterface $input The input to be compared with the expected structure.
     * @param StructureInterface $expected The expected structure that the input should adhere to.
     * @return bool
     */
    public function runPredicates(AbstractConstructure $constructure, StructureInterface $input, StructureInterface $expected): bool;

    /**
     * The user-friendly name for the audit, which can be displayed to the user or used when logged.
     *
     * @return string
     */
    public static function getName(): string;

}