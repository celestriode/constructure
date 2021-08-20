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
     * @param mixed ...$inputs Any input to pass to the events being fired.
     * @return self
     */
    public function trigger(string $name, ...$inputs): self;

    /**
     * Runs an event function with the provided inputs.
     *
     * @param callable $event The event to run.
     * @param mixed ...$inputs The inputs to supply to the event.
     */
    public function runEvent(callable $event, ...$inputs): void;

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
     * @param CapturedEvent $capturedEvent The captured event.
     * @return self
     */
    public function addCapturedEvent(CapturedEvent $capturedEvent): self;

    /**
     * Returns all captured events.
     *
     * @return CapturedEvent[]
     */
    public function getCapturedEvents(): array;
}