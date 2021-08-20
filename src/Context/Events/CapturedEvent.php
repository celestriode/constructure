<?php namespace Celestriode\Constructure\Context\Events;

/**
 * An enclosure for a captured event.
 *
 * @package Celestriode\Constructure\Context\Events
 */
class CapturedEvent
{
    /**
     * @var string The name of the captured event.
     */
    protected $name;

    /**
     * @var callable The event itself.
     */
    protected $function;

    /**
     * @var array The inputs for the event.
     */
    protected $inputs;

    public function __construct(string $name, callable $function, array $inputs)
    {
        $this->name = $name;
        $this->function = $function;
        $this->inputs = $inputs;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return callable
     */
    public function getFunction(): callable
    {
        return $this->function;
    }

    /**
     * @return array
     */
    public function getInputs(): array
    {
        return $this->inputs;
    }

}