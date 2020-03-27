<?php

namespace DarkGhostHunter\Larakick\Lexing\Http\Logics;

use Illuminate\Support\Fluent;

/**
 * Class Authorize
 *
 * @package DarkGhostHunter\Larakick\Parser\Http\Logics
 *
 * @property null|bool $using
 *
 * @property string $ability
 * @property \Illuminate\Support\Collection|string[] $parameters
 */
class Authorize extends Fluent
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
            'parameters' => collect(),
        ], $attributes));
    }
}
