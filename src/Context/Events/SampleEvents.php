<?php namespace Celestriode\Constructure\Context\Events;

use Celestriode\Constructure\Context\AuditInterface;
use Celestriode\Constructure\Structures\StructureInterface;

/**
 * Small collection of example events to get an idea of what to use them for.
 * 
 * Don't actually use these for anything more than messing around!
 * 
 * Pretend that echoing is logging.
 */
final class SampleEvents
{
    public static function logMessage(): callable
    {
        return function(string $message) {

            echo $message;
        };
    }

    public static function auditsStart(): callable
    {
        return function(StructureInterface $input, StructureInterface $expected, AuditInterface ...$audits) {

            echo "Starting audits for input: {$input->toString()}";
        };
    }

    public static function auditRunning(): callable
    {
        return function(AuditInterface $audit, StructureInterface $input, StructureInterface $expected) {

            echo "Running audit '{$audit::getName()}' for input: {$input->toString()}";
        };
    }

    public static function auditPassed(): callable
    {
        return function(AuditInterface $audit, StructureInterface $input, StructureInterface $expected) {

            echo "Audit '{$audit::getName()}' has passed for input: {$input->toString()}";
        };
    }

    public static function auditFailed(): callable
    {
        return function(AuditInterface $audit, StructureInterface $input, StructureInterface $expected) {

            echo "Audit '{$audit::getName()}' has failed for input: {$input->toString()}";
        };
    }

    public static function auditsComplete(): callable
    {
        return function(int $failedCount, StructureInterface $input, StructureInterface $expected, AuditInterface ...$audits) {

            echo "All audits have completed for input: {$input->toString()}";
        };
    }
}