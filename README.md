# Constructure

The Constructure library is a generic template that standardizes structure validation libraries stemming from this one. It comes with few features, focusing more on describing the bare minimum that all extending libraries should follow.

### Extending libraries

The following libraries make use of Constructure to validate specific data structures:

- [JSON Constructure](https://github.com/celestriode/json-constructure) (not yet updated, WIP)
- NBT Constructure (WIP)

## What is Constructure used for?

Structure evaluation and validation from user input, such as the user submitting a string of JSON that should follow a highly specific structure. Because this focuses on user input, which tends to be strings, a parser should typically accompany extending libraries depending on the language of the structure being validated.

## Basic extending

### The AbstractConstructure object

To start, you should extend this class. It comes with a preset `validate()` method to compare two structures (an input and an expected structure). You must supply an event handler when instantiating the object. An event handler comes with the library, but feel free to extend it or implement your own through `EventHandlerInterface`.

One method must be added: `toStructure()`, which will take in some form of input (usually a string) and transforms it into a structure as defined by Constructure.

```php
$constructure = new class(new EventHandler()) extends AbstractConstructure {

    public function toStructure($input): StructureInterface
    {
        // Put code to transform the input into a Constructure structure.
    }
};
```

### Structures

All structures need to implement `StructureInterface` or extend the `AbstractStructure` class, which implements most of the required methods. By default, Constructure does not assume much about the structure of the data structure. With the [JSON Constructure](https://github.com/celestriode/json-constructure) library, each data type is its own structure.

The only method not provided is `toString()`, which you would use to turn a Constructure structure into a string. If the user inputs a string, this should be able to reduce back to that same string. Since the JSON Constructure library uses a structure for each data type, this means that you can use the `toString()` method to provide context for an incorrect part of the user's input.

```php
$structure = new class extends AbstractStructure {

    public function toString(PrettifierInterface $prettifier = null): string
    {
        return "";
    }
};
```

Two structures are always needed for validation: one that the user inputs and is built with `$constructure->toStructure()` and another that describes the structure that the user input must match. Comparison is then performed with the `$constructure->validate()` method.

Structure validation makes use of audits while feedback uses event handling.

#### Prettifying

When using the `toString()` method, a custom prettifier can be supplied to tranform the structure into a prettier string (such as adding newlines and tabs to a minified JSON structure).

### Event handling

Events can be triggered throughout the validation process. What the events do is up to you, as an event is purely a callable object. The event handler instance given to the `$constructure` object will contain all the necessary events.

Events are added using the `addEvent()` method. The inputs to the anonymous function depend on the trigger, so make sure you match up your triggers with your events. Multiple events with the same name can be added and all will be triggered at the same time.

```php
$eventHandler = (new EventHandler())->addEvent("event_name", function ($input1, $input2, $input3) {
    
    // Do something here.
});
```

Triggering the event involves invoking the event name and providing any inputs, if necessary.

```php
$eventHandler->trigger("event_name", 1, "two", 3);
```

Some sample events are provided in `SampleEvent`.

### Audits

By default, the `AbstractStructure` class runs audits during comparison and does nothing else. Audits are a way to inject validation into structures. With no audits, the input and expected structures are considered to be a perfect match. See the [JSON Constructure](https://github.com/celestriode/json-constructure) library for more extensive examples of auditing.

There is a sample `IsNumber` audit provided which shows how an audit might be used. It should otherwise not be used. However, three other events are available for use: `AlwaysTrue` and `AlwaysFalse`, which do what they're named for, and `TriggerEvent`, which is less of an audit and more of an event trigger injection.

The `AbstractAudit` class provides most functionality. The `audit()` and `getName()` methods must still be implemented by you.

```php
$audit = new class extends AbstractAudit {

    public function audit(AbstractConstructure $constructure, StructureInterface $input, StructureInterface $expected): bool
    {
        return true;
    }

    public static function getName(): string
    {
        return "";
    }
}
```

Audits should be triggering events based on what the audit finds about the input structure.