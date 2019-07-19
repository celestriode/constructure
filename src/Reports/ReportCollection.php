<?php namespace Celestriode\Constructure\Reports;

final class ReportCollection implements \Countable
{
    /** @var array $reports The reports held by this collection. */
    private $reports = [];

    /**
     * Holds multiple reports for integrity and for some special interaction.
     *
     * @param MessageInterface ...$reports Optional reports to add on init.
     */
    public function __construct(MessageInterface ...$reports)
    {
        $this->addReports(...$reports);
    }

    /**
     * Adds reports to this collection.
     *
     * @param MessageInterface ...$reports Reports to add to the list.
     * @return void
     */
    public function addReports(MessageInterface ...$reports): void
    {
        for ($i = 0, $j = count($reports); $i < $j; $i++) {
            $this->reports[] = $reports[$i];
        }
    }

    /**
     * Returns all the reports in this collection.
     *
     * @return array
     */
    public function getReports(): array
    {
        return $this->reports;
    }

    /**
     * Returns the number of reports in the collection.
     *
     * @return void
     */
    public function count()
    {
        return count($this->reports);
    }
}
