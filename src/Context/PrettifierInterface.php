<?php namespace Celestriode\Constructure\Context;

use Celestriode\Constructure\Structures\StructureInterface;

/**
 * A simple prettifier definition. A prettifier is used to, of course, prettify a structure's stringified version.
 *
 * @package Celestriode\Constructure\Context
 */
interface PrettifierInterface
{
    public function prettify(StructureInterface $structure): string;
}