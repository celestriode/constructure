<?php namespace Celestriode\Constructure\Reports;

use Celestriode\Constructure\Reports\Severities\SeverityInterface;

interface ReportsInterface
{
    /**
     * Adds a message with optional severity to the collection.
     *
     * @param MessageInterface $report The report to add to the collection.
     * @param SeverityInterface $severity The severity of the message.
     * @return void
     */
    public function addReport(MessageInterface $report): void;

    /**
     * Gets all reports, or just reports of the specified severities.
     *
     * @param string|null ...$severities The class names of the severities to grab reports of.
     * @return ReportCollection
     */
    public function getReports(?string ...$severities): ReportCollection;
}