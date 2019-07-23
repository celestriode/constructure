<?php namespace Celestriode\Constructure;

use Celestriode\Constructure\Reports\ContextInterface;

/**
 * Describes an input from the user that is to be compared to the
 * expected structure.
 */
interface InputInterface
{
    /**
     * Returns the input as context.
     *
     * Basically just do "return $this;" if the input is also the context, unless you have more complex things to do.
     *
     * @return ContextInterface
     */
    public function getContext(): ContextInterface;

    /**
     * Returns the direct parent structure of this input. If there is none, returns null.
     *
     * @return InputInterface|null
     */
    public function getParentInput(): ?InputInterface;
}
