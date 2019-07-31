<?php namespace Celestriode\Constructure;

use Celestriode\Constructure\Reports\ReportsInterface;
use Celestriode\Constructure\Statistics\Statistics;
use Celestriode\Constructure\Audits\AuditInterface;
use Celestriode\Constructure\Predicates\PredicateInterface;
use Celestriode\Constructure\Exceptions\AuditFailed;
use Celestriode\Constructure\Reports\ReportCollection;

/**
 * A standard structure optionally available for all structures to extend.
 *
 * Handles audits in a generic fashion.
 */
abstract class AbstractExpectedStructure implements StructureInterface
{
    /** @var \SplObjectStorage $audits The audits, stored as audit->predicate key/pair values. */
    private $audits;

    public function __construct()
    {
        $this->audits = new \SplObjectStorage();
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
     * Returns the audits for this structure.
     *
     * @return \SplObjectStorage
     */
    final public function getAudits(): \SplObjectStorage
    {
        return $this->audits;
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
