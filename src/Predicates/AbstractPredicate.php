<?php namespace Celestriode\Constructure\Predicates;

use Celestriode\Constructure\Utils\MultiSingletonTrait;

/**
 * A standard predicate optionally available for all custom predicates to extend.
 * 
 * Provides access to ::instance().
 */
abstract class AbstractPredicate implements PredicateInterface
{
    use MultiSingletonTrait;
}