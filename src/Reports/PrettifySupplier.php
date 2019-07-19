<?php namespace Celestriode\Constructure\Reports;

interface PrettifySupplier
{
    /**
     * Takes in an ugly string and transforms it through whatever means necessary to make it pretty.
     *
     * Returns the pretty string.
     *
     * @param string $string The string to prettify.
     * @return string
     */
    public function prettify(string $string): string;
}
