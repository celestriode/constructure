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

    /**
     * Prettifies a string that is designated as a value.
     *
     * @param string $value The string to prettify.
     * @return string
     */
    public function prettifyValue(string $value): string;

    /**
     * Prettifies a string that is designated as a key.
     *
     * @param string $key The string to prettify.
     * @return string
     */
    public function prettifyKey(string $key): string;

    /**
     * Prettifies a string using a supplied closure.
     *
     * @param string $string The string to prettify using the closure.
     * @param \Closure $func The closure to prettify the string with.
     * @return string
     */
    public function prettifyDynamic(string $string, \Closure $func): string;
}
