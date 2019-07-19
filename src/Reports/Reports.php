<?php namespace Celestriode\Constructure\Reports;

use Celestriode\Constructure\Reports\Severities\SeverityInterface;

class Reports implements ReportsInterface
{
    /** @var array $reports Array of MessageInterface: reports with a severity. */
    private $reports = [];
    /** @var array $reports Array of MessageInterface: reports without a severity. */
    private $noSeverityReports = [];

    public function __construct(MessageInterface ...$reports)
    {
        $this->addReports(...$reports);
    }

    /**
     * Adds multiple established reports to the list of reports.
     *
     * @param MessageInterface ...$reports The reports to add.
     * @return void
     */
    public function addReports(MessageInterface ...$reports): void
    {
        for ($i = 0, $j = count($reports); $i < $j; $i++) {

            $this->addReport($reports[$i], $reports[$i]->getSeverity());
        }
    }

    /**
     * Adds a single report to the list of reports.
     * 
     * Reports are separated by severity.
     * 
     * If no severity is specified, the reports are held in and accessed from a separate list.
     *
     * @param MessageInterface $report The report to add.
     * @return void
     */
    public function addReport(MessageInterface $report): void
    {
        if ($report->getSeverity() === null) {

            // If no severity, add it to separate list.

            $this->noSeverityReports[] = $report;
        } else {

            // Otherwise add it to normal list.

            $this->reports[get_class($report->getSeverity())][] = $report;
        }
    }

    /**
     * Returns all reports (including those without severity), or only reports of the specified severity class name.
     * 
     * Specify a severity as NULL in order to include reports without a severity.
     *
     * @param string|null ...$severities The class names of the severities to grab reports of.
     * @return ReportCollection
     */
    public function getReports(?string ...$severities): ReportCollection
    {
        // Return all reports.

        if (empty($severities)) {

            $buffer = [];

            foreach ($this->reports as $reports) {

                foreach ($reports as $report) {

                    $buffer[] = $report;
                }
            }

            return new ReportCollection(...array_merge($this->noSeverityReports, $buffer));
        }

        // Remove duplicate severities.

        $severities = array_unique($severities);

        // Return only reports of the specified severities. If a severity is NULL, then reports without severity are included.

        $buffer = [];

        // Cycle through each severity.

        for ($i = 0, $j = count($severities); $i < $j; $i++) {

            if ($severities[$i] === null) {

                // If null, add reports without severity.

                $buffer = array_merge($buffer, $this->noSeverityReports);
            } else if (isset($this->reports[$severities[$i]])) {

                // Add reports of the specified severity.

                $buffer = array_merge($buffer, $this->reports[$severities[$i]]);
            }

            // If no severity of the type was tracked, then it's simply not tracked.
        }

        // Return the completed collection.

        return new ReportCollection(...$buffer);
    }
}