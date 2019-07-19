<?php namespace Celestriode\Constructure\Audits;

use Celestriode\Constructure\Utils\MultiSingletonTrait;

/**
 * A standard audit optionally available for all audits to extend.
 * 
 * Provides access to ::instance().
 */
abstract class AbstractAudit implements AuditInterface
{
    use MultiSingletonTrait;
}
