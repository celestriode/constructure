<?php namespace Celestriode\Constructure;

use Celestriode\Constructure\Reports\ReportsInterface;
use Celestriode\Constructure\Statistics\Statistics;
use Celestriode\Constructure\Audits\AuditInterface;
use Celestriode\Constructure\Predicates\PredicateInterface;
use Celestriode\Constructure\Exceptions\AuditFailed;
use Celestriode\Constructure\Reports\ReportCollection;
use Celestriode\Constructure\Reports\MessageInterface;

/**
 * A standard structure optionally available for all structures to extend.
 *
 * Handles audits in a generic fashion.
 */
abstract class AbstractExpectedStructure implements StructureInterface
{
    /** @var \SplObjectStorage $audits The audits, stored as audit->predicate key/pair values. */
    private $audits;
    /** @var ReportCollection $structureReports A collection of reports generated specifically for this structure. */
    private $structureReports;

    public function __construct()
    {
        $this->audits = new \SplObjectStorage();
        $this->structureReports = new ReportCollection();
    }

    /**
     * Adds audits to the structure. A predicate may be included to prevent auditing if the predicate fails.
     *
     * @param AuditInterface $audit The audit to perform.
     * @param PredicateInterface $predicate The optional test that must succeed before performing the audit.
     * @return void
     */
    final public function addAudit(AuditInterface $audit, PredicateInterface $predicate = null): self
    {
        $this->audits->attach($audit, $predicate);

        return $this;
    }

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
        $this->getStructureReports()->addReports($message);
        $reports->addReport($message);
    }

    /**
     * Returns the audits for this structure.
     *
     * @return \SplObjectStorage
     */
    final public function getAudits(): \SplObjectStorage
    {
        return $this->audits;
    }

    /**
     * Returns reports relevant only to this structure.
     *
     * @return ReportCollection
     */
    final public function getStructureReports(): ReportCollection
    {
        return $this->structureReports;
    }

    /**
     * Runs extra audits against the incoming input if desired.
     *
     * @param InputInterface $input The input to audit.
     * @param ReportsInterface $reports Reports to add to.
     * @param Statistics $statistics Statistics to manipulate.
     * @return bool
     */
    public function performAudits(InputInterface $input, ReportsInterface $reports, Statistics $statistics): bool
    {
        // Cycle through each audit.

        $successful = true;

        /** @var AuditInterface $audit */
        foreach ($this->audits as $audit) {

            // The value is the predicate.

            /** @var PredicateInterface $predicate */
            $predicate = $this->audits[$audit];

            // If the predicate exists...

            if ($predicate !== null) {

                // And if the test fails, do not perform the audit.

                if (!$predicate->test($input)) {
                    continue;
                }
            }

            // Otherwise perform the audit.

            try {
                if (!$audit->audit($input, $this, $reports, $statistics)) {

                    $successful = false;
                }
            } catch (AuditFailed $exc) {

                // If the audit failed in some way, store the message.

                $reports->addReport($exc->getReportMessage());
                $successful = false;
            }
        }
		
		// Return whether or not all audits were successful.

        return $successful;
    }
}
