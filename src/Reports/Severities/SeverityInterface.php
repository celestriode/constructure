<?php namespace Celestriode\Constructure\Reports\Severities;

/**
 * Describes a type of severity.
 */
interface SeverityInterface
{
    /**
     * The percentage in how "severe" the severity is.
     *
     * For example, debug is at 0.0 while fatal is at 1.0.
     *
     * Use this for formatting or whatever else.
     *
     * @return integer
     */
    public function getPercent(): float;
}
