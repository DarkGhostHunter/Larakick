<?php

namespace DarkGhostHunter\Larakick\Lexing\Code;

use Illuminate\Support\Str;
use Illuminate\Support\Fluent;

/**
 * Class Method
 *
 * @package DarkGhostHunter\Larakick\Parser
 *
 * @property string $name
 * @property \Illuminate\Support\Collection|\DarkGhostHunter\Larakick\Lexing\Code\Argument[] $arguments
 */
class Method extends Fluent
{
    /**
     * Parses a Method line.
     *
     * @param  string  $method
     * @return \DarkGhostHunter\Larakick\Lexing\Code\Method
     */
    public static function parseMethod(string $method)
    {
        $arguments = explode(',', Str::after($method, ':'));

        foreach ($arguments as $key => $argument) {
            $arguments[$key] = Argument::fromString($argument);
        }

        return new static([
            'name' => Str::before($method, ':'),
            'arguments' => collect($arguments)
        ]);
    }
}
