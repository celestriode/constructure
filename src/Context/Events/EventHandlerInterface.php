<?php namespace Celestriode\Constructure\Context\Events;

/**
 * A template for event handlers. Far more specific than a lot of Constructure, as event handlers are essentially how
 * you will provide feedback.
 *
 * @package Celestriode\Constructure\Context\Events
 */
interface EventHandlerInterface
{
    /**
     * Adds an event to be triggered later. Can have multiple events with the same name.
     *
     * @param string $name The name of the event.
     * @param callable $event The event itself (anonymous function).
     * @return self
     */
    public function addEvent(string $name, callable $event): self;

    /**
     * Returns all events stored.
     *
     * @return array
     */
    public function getEvents(): array;

    /**
     * Triggers all events stored under the specified name.
     *
     * @param string $name The name of the event to fire.
     * @param mixed ...$input Any input to pass to the events being fired.
     * @return self
     */
    public function trigger(string $name, ...$input): self;

    /**
     * Mute the event handler, preventing the triggering of events. Use this to ignore
     * audit feedback for certain tasks, such as using audits as silent predicates.
     *
     * @return self
     */
    public function mute(): self;

    /**
     * Unmute the event handler.
     *
     * @return self
     */
    public function unmute(): self;

    /**
     * Returns whether or not the event handler has been muted.
     *
     * @return boolean
     */
    public function silent(): bool;

    /**
     * Sets the event handler to capture events rather than triggering them.
     * Captured events can be run manually later or cleared.
     *
     * @return self
     */
    public function capture(): self;

    /**
     * Returns whether or not events are being captured.
     *
     * @return bool
     */
    public function capturing(): bool;

    /**
     * Runs any captured events.
     *
     * @return self
     */
    public function release(): self;

    /**
     * Clears captured events without running them.
     *
     * @return self
     */
    public function clear(): self;

    /**
     * Captures an event.
     *
     * @param string $name The name of the event that was captured.
     * @param callable $event The event that was captured.
     * @param mixed ...$input The input that was to be passed to the event that was captured.
     * @return self
     */
    public function addCapturedEvent(string $name, callable $event, ...$input): self;

    /**
     * Returns all captured events.
     *
     * @return array
     */
    public function getCapturedEvents(): array;
}