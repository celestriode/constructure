<?php namespace Celestriode\Constructure\Context\Events;

/**
 * A very simple event-handling class that structure comparison can use for giving feedback.
 *
 * @package Celestriode\Constructure\Context\Events
 */
class EventHandler implements EventHandlerInterface
{
    /**
     * Set of event names related to manipulating the event handler.
     */
    public const CAPTURED_CLEARED = '6e5ba5e5-f324-4429-956b-d8868028c903';
    public const CAPTURED_RELEASED = 'f7d2fe2c-b7f1-4c80-9b4e-b5c22f1009db';
    public const CAPTURED = 'f698fa19-02ff-4c0f-bd70-ab49677c2dcb';

    /**
     * @var array All events in this instance of event handler. A single name to a list of events.
     */
    private $events = [];

    /**
     * @var boolean Whether or not any events should be triggered. Use this for a temporary mute.
     */
    private $silent = false;

    /**
     * @var boolean Whether or not events are captured instead of triggered. Captured events can be run with release()
     * or cleared with clear().
     */
    private $capturing = false;

    /**
     * @var array Events captured to be used or cleared later manually. Captured events can be run with release() or
     * cleared with clear().
     */
    private $captured = [];

    public function __construct(bool $defaultEvents = true)
    {
        // If default events should be added, do so.

        if ($defaultEvents) {

            $this->addEvent(self::CAPTURED_RELEASED, self::getCapturedReleasedEvent());
        }
    }

    /**
     * Returns the event function that handles triggering captured events that have been released.
     *
     * @return callable
     */
    public static function getCapturedReleasedEvent(): callable
    {
        return function(EventHandlerInterface $eventHandler, CapturedEvent ...$capturedEvents) {

            // Cycle through all captured events.

            foreach ($capturedEvents as $capturedEvent) {

                // Run the captured event through the supplied event handler.

                $eventHandler->runEvent($capturedEvent->getFunction(), ...$capturedEvent->getInputs());
            }
        };
    }

    /**
     * Adds an event to be triggered later. Can have multiple events with the same name.
     *
     * @param string $name The name of the event.
     * @param callable $event The event itself (anonymous function).
     * @return EventHandlerInterface
     */
    public function addEvent(string $name, callable $event): EventHandlerInterface
    {
        $this->events[$name][] = $event;

        return $this;
    }

    /**
     * Returns all events stored.
     *
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * Triggers all events stored under the specified name.
     *
     * @param string $name The name of the event to fire.
     * @param mixed ...$inputs Any input to pass to the events being fired.
     * @return EventHandlerInterface
     */
    public function trigger(string $name, ...$inputs): EventHandlerInterface
    {
        // If events aren't muted and events with the given name exists...

        if (!$this->silent() && isset($this->getEvents()[$name])) {

            // Fire all events.

            foreach ($this->getEvents()[$name] as $event) {

                // If capturing is enabled, store the event for later.

                if ($this->capturing()) {

                    $this->addCapturedEvent(new CapturedEvent($name, $event, $inputs));

                    $this->trigger(self::CAPTURED, $name, $event, $inputs, $this);

                } else {

                    // Otherwise, run the event.

                    $this->runEvent($event, ...$inputs);
                }
            }
        }

        return $this;
    }

    /**
     * Runs an event function with the provided inputs.
     *
     * @param callable $event The event to run.
     * @param mixed ...$inputs The inputs to supply to the event.
     */
    public function runEvent(callable $event, ...$inputs): void
    {
        call_user_func($event, ...$inputs);
    }

    /**
     * Mute the event handler, preventing the triggering of events. Use this to ignore
     * audit feedback for certain tasks, such as using audits as silent predicates.
     *
     * @return EventHandlerInterface
     */
    public function mute(): EventHandlerInterface
    {
        $this->silent = true;

        return $this;
    }

    /**
     * Unmute the event handler.
     *
     * @return EventHandlerInterface
     */
    public function unmute(): EventHandlerInterface
    {
        $this->silent = false;

        return $this;
    }

    /**
     * Returns whether or not the event handler has been muted.
     *
     * @return boolean
     */
    public function silent(): bool
    {
        return $this->silent;
    }

    /**
     * Sets the event handler to capture events rather than triggering them.
     * Captured events can be run manually later or cleared.
     *
     * @return EventHandlerInterface
     */
    public function capture(): EventHandlerInterface
    {
        $this->capturing = true;

        return $this;
    }

    /**
     * Returns whether or not events are being captured.
     *
     * @return bool
     */
    public function capturing(): bool
    {
        return $this->capturing;
    }

    /**
     * Runs any captured events.
     *
     * @return EventHandlerInterface
     */
    public function release(): EventHandlerInterface
    {
        // Clear captured events and run a trigger that indicates they've been released.

        $this->capturing = false;
        $this->trigger(self::CAPTURED_RELEASED, $this, ...$this->getCapturedEvents());
        $this->captured = [];

        return $this;
    }

    /**
     * Clears captured events without running them.
     *
     * @return EventHandlerInterface
     */
    public function clear(): EventHandlerInterface
    {
        $this->capturing = false;
        $this->trigger(self::CAPTURED_CLEARED, $this, ...$this->getCapturedEvents());
        $this->captured = [];

        return $this;
    }

    /**
     * Captures an event.
     *
     * @param CapturedEvent $capturedEvent The captured event.
     * @return EventHandlerInterface
     */
    public function addCapturedEvent(CapturedEvent $capturedEvent): EventHandlerInterface
    {
        $this->captured[] = $capturedEvent;

        return $this;
    }

    /**
     * Returns all captured events.
     *
     * @return CapturedEvent[]
     */
    public function getCapturedEvents(): array
    {
        return $this->captured;
    }
}