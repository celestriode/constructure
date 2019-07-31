<?php namespace Celestriode\Constructure\Utils;

use Celestriode\Constructure\Reports\MessageInterface;
use Celestriode\Constructure\Reports\ReportsInterface;
use Celestriode\Constructure\Reports\ReportCollection;

/**
 * Allows usage of class::instance() to return a new instance of that class.
 *
 * Can be applied to an abstract class to function correctly through inheritance.
 *
 * Primarily used with audits and predicates to reduce overhead from duplicate objects.
 */
trait StructureReportsTrait
{
    /** @var ReportCollection $structureReports A collection of reports generated specifically for this structure. */
    private $structureReports;

    /**
     * Adds a report message to the structure itself, as well as
     * to the full reports, killing two birds with one stone.
     *
     * @param MessageInterface $message The message to add to the structure.
     * @param ReportsInterface $reports The reports to add the message to.
     * @return void
     */
    final public function addStructureReport(MessageInterface $message, ReportsInterface $reports): void
    {
        if ($this->structureReports === null) {

            // Instantiate report collection if not existent.

            $this->structureReports = new ReportCollection();
        }
        
        // Add message to input and reports.

        $this->getStructureReports()->addReports($message);
        $reports->addReport($message);
    }

    /**
     * Returns reports relevant only to this structure.
     *
     * @return ReportCollection
     */
    final public function getStructureReports(): ReportCollection
    {
        if ($this->structureReports === null) {

            // Instantiate report collection if not existent.

            $this->structureReports = new ReportCollection();
        }

        // Return all reports on this input.

        return $this->structureReports;
    }
}
