<?php namespace Celestriode\Constructure\Utils;

/**
 * Helper methods for report messages, such as input sanitization and formatting.
 */
class MessageUtils
{
    /**
     * Formats input as a key surrounded by quotes for display in HTML.
     *
     * @param string ...$keys Keys to sanitize and format.
     * @return string
     */
    public static function key(string ...$keys): string
    {
        $buffer = '';

        for ($i = 0, $j = count($keys); $i < $j; $i++) {
            $buffer .= '"' . htmlentities($keys[$i]) . '"';

            if ($i + 1 < $j) {
                $buffer .= ', ';
            }
        }

        return $buffer;
    }

    /**
     * Formats input as a value surrounded by <code> tags for display in HTML.
     *
     * @param string ...$values Values to sanitize and format.
     * @return string
     */
    public static function value(string ...$values): string
    {
        $buffer = '';

        for ($i = 0, $j = count($values); $i < $j; $i++) {
            $buffer .= '<code>' . htmlentities($values[$i]) . '</code>';

            if ($i + 1 < $j) {
                $buffer .= ', ';
            }
        }

        return $buffer;
    }
}
