<?php namespace Celestriode\Constructure;

use Celestriode\Constructure\Reports\ContextInterface;
use Celestriode\Constructure\Reports\ReportCollection;
use Celestriode\Constructure\Reports\MessageInterface;
use Celestriode\Constructure\Reports\ReportsInterface;

/**
 * Describes an input from the user that is to be compared to the
 * expected structure.
 */
interface InputInterface
{
    /**
     * Returns the input as context.
     *
     * Basically just do "return $this;" if the input is also the context, unless you have more complex things to do.
     *
     * @return ContextInterface
     */
    public function getContext(): ContextInterface;

    /**
     * Returns the direct parent structure of this input. If there is none, returns null.
     *
     * @return InputInterface|null
     */
    public function getParentInput(): ?InputInterface;

    /**
     * Adds a report message to the input structure itself, as well as
     * to the full reports, killing two birds with one stone.
     *
     * @param MessageInterface $message The message to add to the structure.
     * @param ReportsInterface $reports The reports to add the message to.
     * @return void
     */
    public function addStructureReport(MessageInterface $message, ReportsInterface $reports): void;

    /**
     * Returns reports relevant only to this structure.
     *
     * @return ReportCollection
     */
    public function getStructureReports(): ReportCollection;
}
