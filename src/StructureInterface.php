<?php namespace Celestriode\Constructure;

use Celestriode\Constructure\Reports\ReportsInterface;
use Celestriode\Constructure\Statistics\Statistics;

/**
 * Describes the expected structure that an input should be compared to.
 */
interface StructureInterface
{
    /**
     * Compares the input to the expected structure.
     *
     * @param InputInterface $input The input to compare with the structure.
     * @param ReportsInterface $reports Reports to add messages to.
     * @param Statistics $statistics Statistics to manipulate.
     * @return boolean
     */
    public function compareStructure(InputInterface $input, ReportsInterface $reports, Statistics $statistics): bool;

    /**
     * Runs extra audits against the incoming input if desired.
     *
     * @param InputInterface $input The input to audit.
     * @param ReportsInterface $reports Reports to add to.
     * @param Statistics $statistics Statistics to manipulate.
     * @return void
     */
    public function performAudits(InputInterface $input, ReportsInterface $reports, Statistics $statistics): void;
}
