<?php

namespace DarkGhostHunter\Larakick\Lexing\Code;

use LogicException;
use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

/**
 * Class Line
 *
 * @package DarkGhostHunter\Larakick\Lexing\Code
 *
 * @property \DarkGhostHunter\Larakick\Lexing\Code\Call $call
 *
 * @property \Illuminate\Support\Collection|\DarkGhostHunter\Larakick\Lexing\Code\Method[] $methods
 */
class Line extends Fluent
{
    /**
     * Create a new fluent instance.
     *
     * @param  array|object  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        parent::__construct(array_merge([
            'methods' => collect(),
        ], $attributes));
    }

    /**
     * Creates a new Line instance from a string.
     *
     * @param  string  $line
     * @param  \Illuminate\Support\Collection  $models
     * @return \DarkGhostHunter\Larakick\Lexing\Code\Line
     */
    public static function fromModelOrVariable(string $line, Collection $models = null)
    {
        $instance = new static;

        $parts = collect(explode(' ', trim($line)));

        $call = $parts->shift();

        if ($models && ctype_upper($call[0])) {
            if (! $instance->call->model = $models->get($call)) {
                throw new LogicException("The [$call] model doesn't exists");
            }
        }
        else {
            $instance->call->variable = $call;
        }

        return static::pushMethods($parts, $instance);
    }

    /**
     * Creates a class
     *
     * @param  string  $line
     * @return \DarkGhostHunter\Larakick\Lexing\Code\Line
     */
    public static function fromFunction(string $line)
    {
        $instance = new static;

        $parts = collect(explode(' ', trim($line)));

        $instance->call->function = $parts->shift();

        return static::pushMethods($parts, $instance);
    }

    /**
     * Returns a Line from a method without starting call.
     *
     * @param  string  $line
     * @return \DarkGhostHunter\Larakick\Lexing\Code\Line
     */
    public static function fromLine(string $line)
    {
        return static::pushMethods(collect(explode(' ', trim($line))), new static);
    }

    /**
     * Pushes the methods from the line
     *
     * @param  \Illuminate\Support\Collection  $methods
     * @param  \DarkGhostHunter\Larakick\Lexing\Code\Line  $line
     * @return \DarkGhostHunter\Larakick\Lexing\Code\Line
     */
    protected static function pushMethods(Collection $methods, Line $line)
    {
        foreach ($methods->skip(1) as $method) {
            $line->methods->push(Method::parseMethod($method));
        }

        return $line;
    }
}
