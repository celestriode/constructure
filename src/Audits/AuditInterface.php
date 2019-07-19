<?php namespace Celestriode\Constructure\Audits;

use Celestriode\Constructure\Reports\ReportsInterface;
use Celestriode\Constructure\Statistics\Statistics;
use Celestriode\Constructure\InputInterface;
use Celestriode\Constructure\StructureInterface;

/**
 * Describes an audit. An audit is used for extra validation that may be repeated
 * many times.
 */
interface AuditInterface
{
    /**
     * Performs extra tasks to validate integrity of input.
     * 
     * Should throw Celestriode\Exceptions\AuditFailed if the audit could not be performed.
     * It should be thrown with the issue message itself rather than creating a report.
     * Use reports instead for "debug", "info", and "warn" severities. But really it's up
     * to you how to do things.
     *
     * @param InputInterface $input The input to audit.
     * @param StructureInterface $expected The expected structure if needed.
     * @param ReportsInterface $reports Reports to add to.
     * @param Statistics $statistics Statistics to manipulate.
     * @return void
     */
    public function audit(InputInterface $input, StructureInterface $expected, ReportsInterface $reports, Statistics $statistics): void;
}
