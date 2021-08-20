# Constructure

The Constructure library is a generic template that standardizes structure validation libraries stemming from this one. It comes with few features, focusing more on describing the bare minimum that all extending libraries should follow.

### Extending libraries

The following libraries make use of Constructure to validate specific data structures:

- [JSON Constructure](https://github.com/celestriode/constructure-json)
- NBT Constructure (WIP)

# What is Constructure used for?

Structure evaluation and validation from user input, such as the user submitting a string of JSON or SNBT that should follow a highly specific structure. Because this focuses on user input, which tends to be strings, a parser should typically accompany extending libraries depending on the language of the structure being validated.

# Basic extending

## The AbstractConstructure object

To start, you should extend this class. It comes with a preset `validate()` method to compare two structures (an input and an expected structure). You must supply an event handler when instantiating the object. An event handler comes with the library, but feel free to extend it or implement your own through `EventHandlerInterface`.

One method must be added to the extending Constructure class: `toStructure()`, which will take in some form of input (usually a string) and transforms it into a structure as defined by Constructure. That resulting structure must be an instance of `StructureInterface`.

```php
$constructure = new class(new EventHandler()) extends AbstractConstructure {

    public function toStructure($input): StructureInterface
    {
        // Put code to transform the input into a Constructure structure.
    }
};
```

## Structures

All structures need to implement `StructureInterface` or extend the `AbstractStructure` class, which implements most of the required methods. By default, Constructure does not assume much about the structure of the data structure. With the [JSON Constructure](https://github.com/celestriode/constructure-json) library, each data type is its own structure. It also includes other specialized structures for defining the expected structure, such as placeholders for user-defined keys and redirects for recursive structures.

The only method not provided by `AbstractStructure` is `toString()`, which you would use to turn a `StructureInterface` back into a string. If the user inputs a string, this should be able to reduce back to that same string. Since the JSON Constructure library uses a structure for each data type, this means that you can use the `toString()` method to provide context for an incorrect part of the user's input.

```php
$structure = new class extends AbstractStructure {

    public function toString(PrettifierInterface $prettifier = null): string
    {
        return "";
    }
};
```

You will need two structures for validation: one that the user inputs and is built with `$constructure->toStructure()` and another that describes the structure that the user input must match. That expected structure is to be built yourself ahead of time. A comparison occurs with the `$constructure->validate()` method.

Structure validation makes use of audits while feedback uses event handling.

### Prettifying

When using the `toString()` method, a custom prettifier can be supplied to tranform the structure into a prettier string (such as adding newlines and tabs to a minified JSON structure). There are no prettifying classes alongside this library.

## Event handling

Events can be triggered throughout the validation process. What the events do is up to you, as an event is purely a callable object. You can either create your own event handler using the `EventHandlerInterface` interface or use the pre-made `EventHandler` class. The event handler instance given to the `$constructure` object will contain all the necessary events.

Events can be added using the `addEvent()` method. The optional inputs to the anonymous function depend on the trigger, so make sure you match up your triggers with your events. Multiple events with the same name can be added and all will be triggered at the same time.

```php
$eventHandler = (new EventHandler())->addEvent("event_name", function ($input1, $input2, $input3) {
    
    // Do something here.
});
```

Triggering the event involves invoking the event name in the `trigger()` method and providing any inputs, if necessary.

```php
$eventHandler->trigger("event_name", 1, "two", 3);
```

You can find sample event functions in `SampleEvents`.

### Event muting

The event handler can be muted to prevent events from being triggered, as though those events do not exist. This is used when running [predicates](#predicates), which are meant to be silent.

The `mute()` method on the event handler will mute it, while `unmute()` will unmute it (and `silent()` will return whether or not it is muted).

### Event capturing

Rather than fully ignoring events, capturing them and determining if they should be ignored or not can be more useful. For example, the `BitwiseAudits` [audit](#audits) uses event capturing to hold off on triggering events until it is certain that the audit fails.

When capturing is enabled with `capture()`, any events triggered will instead be set aside for later. Once you are done with capturing, you do one of two things:

1. Use the `clear()` method, which wipes out all the captured events without running them.
2. Use the `release()` method, which disables capturing and runs the events that were captured during the capture period.

Captured events are triggered through the `EventHandler::CAPTURED_RELEASED` default event. It being a default event allows you to change how captured events are handled upon release.

### Default events

Default events are events that any instance of `EventHandler` will include by default. Instantiating the event handler with `new EventHandler(false)` will disable the inclusion of default events.

Currently, the only default event is for releasing captured events. If you want that particular event's function, the `getReleaseCapturedEvent()` static method will provide it.

## Audits

An audit is procedure to validate the input structure. In order for an input structure to be considered correct, all audits must pass for the input when comparing it to the expected structure.

All audits must implement `AuditInterface`, though there is an `AbstractAudit` class that provides most functionality. 2 methods must be implemented by you:

1. `audit()`, which will check the input structure and return true or false, depending on whether the input is correct based on the audit. The Constructure object is also provided, which gives access to the event handler, as well as the expected structure for extra validation.
2. `getName()`, which returns a use-friendly name of the audit, which can be displayed to the end-user if providing feedback based on failed (or successful) audits.

```php
$audit = new class extends AbstractAudit {

    public function audit(AbstractConstructure $constructure, StructureInterface $input, StructureInterface $expected): bool
    {
        return true;
    }

    public static function getName(): string
    {
        return "user-friendly name of the audit";
    }
};
```

Audits should be triggering events based on what the audit finds about the input structure. Events can be used for logging or for user feedback.

By default, the `AbstractStructure` class runs audits during comparison and does nothing else. With no audits, the input and expected structures are considered to be a perfect match. See the [JSON Constructure](https://github.com/celestriode/constructure-json) library for more extensive examples of auditing.

There is a sample `IsNumber` audit provided which shows how you can use an audit for input validation and event triggering. It should otherwise not be used. However, four other audits are available for use:

1. `AlwaysTrue`, which is an audit that always passes.
2. `AlwaysFalse`, which is an audit that always fails.
3. `TriggerEvent`, which is less of an audit and more of an event injector. This is a deferred audit, such that it runs after non-deferred audits do. This can be useful for analyzing the structure after auditing it.
4. `BitwiseAudits`, which takes in an operator (OR, XOR, or AND) and a set of audits, and then performs the operation based on the results of the set of audits together. This audit will pass or fail based on that operation, rather than passing or failing for each individual audit. AND is not included because that is how audits work by default.

### Predicates

A predicate is an audit that acts as a condition that must pass before another audit can run. The predicate runs silently, triggering no events, and returns whether or not the predicate passes. If the predicate fails, the primary audit will not run (and thus will not be used to pass or fail an input structure). If it passes, the primary audit will then run.

Predicates can be added to an audit instance using `addPredicates()` or `addPredicate()`. The `AbstractStructure` class (**not** `AbstractAudit`) will handle predicates, so if you are creating your own implementation of `StructureInterface`, be sure to handle it too.