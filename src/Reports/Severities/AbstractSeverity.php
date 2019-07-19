<?php namespace Celestriode\Constructure\Reports\Severities;

abstract class AbstractSeverity implements SeverityInterface
{
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
