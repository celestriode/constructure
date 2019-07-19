<?php namespace Celestriode\Constructure;

use Celestriode\Constructure\Reports\ReportsInterface;
use Celestriode\Constructure\Statistics\Statistics;

/**
 * Stores information about the result of validating a structure.
 *
 * This can include numerous things, such as a simple bool, the generated reports,
 * and statistics.
 *
 * This is provided by Celestriode\Constructure\Constructure::validate().
 */
class Results
{
    /** @var bool $succeeds Whether or not the structure was successfully validated without issues. */
    private $succeeds;

    /** @var ReportsInterface $reports The reports generated via validation. */
    private $reports;

    /** @var Statistics $statistics The statistics generated via validation. */
    private $statistics;

    public function __construct(bool $successful, ReportsInterface $reports, Statistics $statistics)
    {
        $this->setSucceeds($successful);
        $this->setReports($reports);
        $this->setStatistics($statistics);
    }

    /**
     * Sets whether or not the structure succeeded.
     *
     * Success is determined by the implemented structural validation via StructureInterface. Return true and false wisely.
     *
     * @param boolean $succeeds True if succeeeded.
     * @return void
     */
    public function setSucceeds(bool $succeeds): void
    {
        $this->succeeds = $succeeds;
    }

    /**
     * Sets the reports of the results.
     *
     * @param ReportsInterface $reports
     * @return void
     */
    public function setReports(ReportsInterface $reports): void
    {
        $this->reports = $reports;
    }

    /**
     * Sets the statistics of the results.
     *
     * @param Statistics $statistics
     * @return void
     */
    public function setStatistics(Statistics $statistics): void
    {
        $this->statistics = $statistics;
    }

    /**
     * Returns whether or not the validation succeeded.
     *
     * @return boolean
     */
    public function succeeds(): bool
    {
        return $this->succeeds;
    }

    /**
     * Returns the reports generated from validating structures.
     *
     * @return ReportsInterface
     */
    public function getReports(): ReportsInterface
    {
        return $this->reports;
    }

    /**
     * Returns the statistics after having validated structures.
     *
     * @return Statistics
     */
    public function getStatistics(): Statistics
    {
        return $this->statistics;
    }
}
