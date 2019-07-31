<?php namespace Celestriode\Constructure\Reports;

/**
 * An optional class to provide to ContextInterface::contextToString() that will handle prettifying the context.
 */
interface PrettifySupplierInterface
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
