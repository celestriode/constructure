<?php namespace Celestriode\Constructure\Predicates;

use Celestriode\Constructure\InputInterface;

class AlwaysTrue extends AbstractPredicate
{
    public function test(InputInterface $input): bool
    {
        return true;
    }
}