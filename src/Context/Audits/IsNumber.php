<?php namespace Celestriode\Constructure\Context\Audits;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\Constructure\Structures\StructureInterface;

/**
 * A sample audit that determines whether or not the input is numeric.
 *
 * As this is a sample, you should not be using it.
 *
 * @package Celestriode\Constructure\Context\Audits
 */
class IsNumber extends AbstractAudit
{
    /**
     * @var boolean If true, the input must not be a PHP string even if it's numeric.
     */
    private $strict;

    /**
     * An example showing where options can be submitted to manipulate how the audit will function.
     *
     * @param boolean $strict Whether the input cannot be a string.
     */
    public function __construct(bool $strict = true)
    {
        $this->strict = $strict;
    }

    /**
     * Return the name of the audit, which in this case is pretty self-explanatory.
     *
     * @return string
     */
    public static function getName(): string
    {
        return "is_number";
    }

    /**
     * Takes in the input structure and the expected structure. The input structure
     * contains the data to audit, while the expected structure contains any context necessary for what
     * is expected for the input. The Constructure does event handling.
     * 
     * This particular audit checks in the input is a number (and not a string). If so, it
     * will trigger an event to log a message. If not, it will trigger an event to log a
     * different message.
     *
     * @param AbstractConstructure $constructure The base Constructure object, which holds the event handler.
     * @param StructureInterface $input The input to be compared with the expected structure.
     * @param StructureInterface $expected The expected structure that the input should adhere to.
     * @return boolean
     */
    public function audit(AbstractConstructure $constructure, StructureInterface $input, StructureInterface $expected): bool
    {
        // Get the raw data.

        $data = $input->getValue();

        // If it's a string and strings aren't allowed, log a message and return false. Use toString() for display purposes.

        if ($this->strict && is_string($data)) {

            $constructure->getEventHandler()->trigger("log_message", "Input ({$input->toString()}) is not allowed to be a string.");

            return false;
        }

        // Otherwise check if it's numeric. Log a message and return true. Use toString() for display purposes.

        if (is_numeric($data)) {

            $constructure->getEventHandler()->trigger("log_message", "Input ({$input->toString()}) is a number.");

            return true;
        }

        // Otherwise, log a message and return false. Use toString() for display purposes.

        $constructure->getEventHandler()->trigger("log_message", "Input ({$input->toString()}) is not a number.");

        return false;
    }
}