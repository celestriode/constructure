<?php namespace Celestriode\Constructure\Utils;

/**
 * Allows usage of class::instance() to return a new instance of that class.
 *
 * Can be applied to an abstract class to function correctly through inheritance.
 *
 * Primarily used with audits and predicates to reduce overhead from duplicate objects.
 */
trait MultiSingletonTrait
{
    /** @var array $instances A collection of classname->object key/value pairs. */
    private static $instances = [];

    /**
     * Returns a singleton of the class.
     *
     * @param mixed ...$data Extra data that may be used by the class.
     * @return self
     */
    final public static function instance(...$data): self
    {
        // Return the instantiated class if existent.

        if (isset(self::$instances[static::class])) {
            return self::$instances[static::class];
        }

        // Create, store, and return a new instance.

        return self::$instances[static::class] = new static(...$data);
    }
}
