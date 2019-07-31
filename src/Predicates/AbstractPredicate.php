<?php namespace Celestriode\Constructure\Predicates;

use Celestriode\Constructure\Utils\MultiSingletonTrait;
use Celestriode\Constructure\Exceptions\AbstractReportException;

/**
 * A standard predicate optionally available for all custom predicates to extend.
 *
 * Provides access to ::instance().
 */
abstract class AbstractPredicate implements PredicateInterface
{
    use MultiSingletonTrait;
    
    /** @var array $issues The optional issues raised by the predicate. */
    private $issues = [];

    /**
     * Adds an exception to the list of exceptions. These can be optionally
     * accessed later on to either throw the exceptions or to add their
     * messages to reports directly.
     *
     * This allows predicates to be silent but still have something to say.
     *
     * @param AbstractReportException $issue The exception to add.
     * @return void
     */
    final public function addIssue(AbstractReportException $issue): void
    {
        $this->issues[] = $issue;
    }

    /**
     * Returns all issues added to this instance of the predicate.
     *
     * @return array
     */
    final public function getIssues(): array
    {
        return $this->issues;
    }
}
