<?php namespace Celestriode\Constructure\Predicates;

use Celestriode\Constructure\InputInterface;

/**
 * Describes a predicate, which simply performs a test against the input.
 */
interface PredicateInterface
{
    /**
     * Performs a test against the input. Predicates are used when you need to silently test
     * a condition against the input, unlike audits which are very loud.
     *
     * @param InputInterface $input The input to test.
     * @return boolean
     */
    public function test(InputInterface $input): bool;
}
