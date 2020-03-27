<?php

namespace DarkGhostHunter\Larakick\Lexing\Http;

use Illuminate\Support\Fluent;

/**
 * Class Controller
 *
 * @package DarkGhostHunter\Larakick\Parser\Http
 *
 * @property string $class
 * @property string $namespace
 *
 * @property \Illuminate\Support\Collection|\DarkGhostHunter\Larakick\Lexing\Code\Method[] $middleware
 *
 * @property bool $isInvokable
 *
 * @property \DarkGhostHunter\Larakick\Lexing\Http\ResourceController $resource
 *
 * @property \Illuminate\Support\Collection|\DarkGhostHunter\Larakick\Lexing\Http\Action[] $actions
 */
class Controller extends Fluent
{
    /**
     * All of the attributes set on the fluent instance.
     *
     * @var array
     */
    protected $attributes = [
        'isInvokable' => false,
        'isResource' => false,
    ];

    /**
     * Create a new fluent instance.
     *
     * @param  array|object  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['middleware'] = collect();
        $this->attributes['resource'] = new ResourceController;
        $this->attributes['actions'] = collect();
    }

    /**
     * Return the Model full namespace.
     *
     * @return string
     */
    public function fullNamespace()
    {
        return trim($this->class, '\\') . '\\' . trim($this->namespace, '\\');
    }
}
