<?php namespace Celestriode\Constructure;

use Celestriode\Constructure\Reports\ReportsInterface;
use Celestriode\Constructure\Statistics\Statistics;

/**
 * Describes the expected structure that an input should be compared to.
 *
 * Essentially just handles the comparing function.
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
}
