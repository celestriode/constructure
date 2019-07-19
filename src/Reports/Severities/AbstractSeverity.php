<?php namespace Celestriode\Constructure\Reports\Severities;

use Celestriode\Constructure\Utils\MultiSingletonTrait;

/**
 * A standard severity class optionally available for all custom severities to extend.
 *
 * Also provides access to ::instance().
 */
abstract class AbstractSeverity implements SeverityInterface
{
    use MultiSingletonTrait;

    /**
     * Returns percent based on the current class for ease of editing.
     *
     * If overriding, you can use parent::getPercent() as a fallback.
     *
     * @return float
     */
    public function getPercent(): float
    {
        switch (static::class) {

            case Debug::class:
                return 0.0;
            case Info::class:
                return 0.1;
            case Warn::class:
                return 0.25;
            case Error::class:
                return 0.5;
            case Fatal::class:
                return 1.0;
            default:
                return 0.75;
        }
    }
}
