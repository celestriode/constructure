<?php namespace Celestriode\Constructure\Predicates;

use Celestriode\Constructure\InputInterface;

class AlwaysFalse extends AbstractPredicate
{
    public function test(InputInterface $input): bool
    {
        return false;
    }
}