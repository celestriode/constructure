<?php namespace Celestriode\Constructure\Structures;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\Constructure\Context\AuditInterface;
use Celestriode\Constructure\Context\PrettifierInterface;

/**
 * A structure contains everything concerning the current data structure, such as the elements within a JSON list
 * or if the field is required in an object. It can also contain a reference to its parent if it supports trees.
 * 
 * Audits can be stored in a structure to perform structure-specific checks, such as verifying a string's
 * value. This can be mixed with global audits stored in the Constructure object.
 *
 * @package Celestriode\Constructure\Structures
 */
interface StructureInterface
{
    /**
     * Takes in a context to compare it to the structure.
     *
     * @param AbstractConstructure $constructure The Constructure object associated with this comparison.
     * @param StructureInterface $other The other structure that should adhere to this structure's audits.
     * @return boolean
     */
    public function compare(AbstractConstructure $constructure, self $other): bool;

    /**
     * Replaces all audits in this context with the input.
     *
     * @param AuditInterface ...$audits The audits to set.
     * @return self
     */
    public function setAudits(AuditInterface ...$audits): self;

    /**
     * Adds multiple audits to the context.
     *
     * @param AuditInterface ...$audits The audits to add.
     * @return self
     */
    public function addAudits(AuditInterface ...$audits): self;

    /**
     * Adds a single audit to the context.
     *
     * @param AuditInterface $audit The audit to add.
     * @return self
     */
    public function addAudit(AuditInterface $audit): self;

    /**
     * Returns all audits.
     *
     * @return AuditInterface[]
     */
    public function getAudits(): array;

    /**
     * Sets the raw input of the structure that would result in this object being created.
     *
     * @param mixed $input The input, whatever it may be.
     * @return self
     */
    public function setValue($input = null): self;

    /**
     * Returns the input, if existent.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Adds an audit that failed to the list of audits that failed.
     *
     * @param AuditInterface $failedAudit The failed audit to add.
     * @return $this
     */
    public function addFailedAudit(AuditInterface $failedAudit): self;

    /**
     * Returns all the audits that failed to pass.
     *
     * @return AuditInterface[]
     */
    public function getFailedAudits(): array;

    /**
     * Sets whether or not the structure has passed audits. The expected structure should be setting this value.
     *
     * @param bool $passed True if the structure passes.
     * @return $this
     */
    public function setPassed(bool $passed = true): self;

    /**
     * Returns whether or not auditing the structure has failed. Should return null when auditing has not yet occurred.
     *
     * @return bool
     */
    public function passed(): ?bool;

    /**
     * Converts the context to a string, typically via the input. Can be prettified, if implemented.
     *
     * @param PrettifierInterface|null $prettifier An optional function to prettify the input.
     * @return string
     */
    public function toString(PrettifierInterface $prettifier = null): string;
}