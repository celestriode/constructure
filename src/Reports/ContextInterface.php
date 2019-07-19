<?php namespace Celestriode\Constructure\Reports;

use Celestriode\Constructure\Statistics\Statistics;

/**
 * Describes structural context, primarily being the structure itself (such as JSON or NBT).
 *
 * For example, with `{"first":{"second":true}}`, the validation will ideally have trimmed
 * the context down to `"second":true` when it gets to that depth. This helps the user
 * pinpoint where an issue is.
 */
interface ContextInterface
{
    /**
     * Turns the context into a string for whatever display purposes necessary.
     *
     * This should be possible for any context because the context is meant to
     * be a structure to validate, and thus should have a string representation
     * for the user to see.
     *
     * @param PrettifySupplier $prettifySupplier Optional function to prettify data with.
     * @return string
     */
    public function contextToString(PrettifySupplier $prettifySupplier = null): string;

    /**
     * Manipulates statistics based on the context.
     *
     * @param Statistics $statistics The statistics to manipulate.
     * @return void
     */
    public function addContextToStats(Statistics $statistics): void;
}
