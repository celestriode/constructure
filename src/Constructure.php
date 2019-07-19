<?php namespace Celestriode\Constructure;

use Celestriode\Constructure\Reports\ReportsInterface;
use Celestriode\Constructure\Statistics\Statistics;
use Celestriode\Constructure\Reports\Reports;

/**
 * Compares an incoming structure, typically provided by a user, and compares it with an expected structure.
 * 
 * Example usage is to ensure custom JSON configuration files for games follow an expected structure.
 * 
 * See celestriode/json-constructure and celestriode/nbt-constructure for example implementations.
 */
class Constructure
{
    /**
     * Compares the input with the expected structure.
     * 
     * Creates reports and statistics if they are not provided.
     *
     * @param InputInterface $input The input to validate.
     * @param StructureInterface $expected The structure to validate it against.
     * @param ReportsInterface $reports Reports to add messages to.
     * @param Statistics $statistics Statistics manipulated by context provided from the input.
     * @return Results
     */
    public static function validate(InputInterface $input, StructureInterface $expected, ReportsInterface $reports = null, Statistics $statistics = null): Results
    {
        $reports = $reports ?? new Reports();
        $statistics = $statistics ?? new Statistics();

        $successful = $expected->compareStructure($input, $reports, $statistics);

        return new Results($successful, $reports, $statistics);
    }
}