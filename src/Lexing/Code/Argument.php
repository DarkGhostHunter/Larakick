<?php

namespace DarkGhostHunter\Larakick\Lexing\Code;

use Illuminate\Support\Str;
use Illuminate\Support\Fluent;

/**
 * Class Argument
 *
 * @package DarkGhostHunter\Larakick\Lexing
 *
 * @property string $string
 *
 * @property string $variable
 * @property string $property
 */
class Argument extends Fluent
{
    /**
     * Checks if the argument is just a string.
     *
     * @return bool
     */
    public function isString()
    {
        return ! $this->variable;
    }

    /**
     * Creates an Argument from a string
     *
     * @param  string  $argument
     * @return static
     */
    public static function fromString(string $argument)
    {
        if (! Str::contains($argument, '.')) {
            return new static([
                'string' => $argument
            ]);
        }

        return new static([
            'variable' => Str::before($argument, ':'),
            'property' => Str::after($argument, ':'),
        ]);
    }

    /**
     * Returns the Argument as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return "'{$this->variable}'" ?? "\${$this->variable}->{$this->property}";
    }
}
