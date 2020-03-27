<?php

namespace DarkGhostHunter\Larakick\Lexing\Http;

use Illuminate\Support\Fluent;

/**
 * Class Action
 *
 * @package DarkGhostHunter\Larakick\Parser\Http
 *
 * @property string $name
 *
 * @property \DarkGhostHunter\Larakick\Lexing\Http\Logics\Route $route
 * @property \Illuminate\Support\Collection|Logics\Model[] $models
 * @property \DarkGhostHunter\Larakick\Lexing\Http\Logics\Authorize $authorize
 *
 * @property string $formRequest
 * @property \Illuminate\Support\Collection|Logics\Validate[]| $validate
 *
 * @property \Illuminate\Support\Collection|Logics\Queries[] $queries
 *
 * @property \DarkGhostHunter\Larakick\Lexing\Http\Logics\Save $save
 * @property \DarkGhostHunter\Larakick\Lexing\Http\Logics\Delete $delete
 *
 * @property \Illuminate\Support\Collection|Logics\Fire[] $fire
 * @property \Illuminate\Support\Collection|Logics\Notify[] $notify
 * @property \Illuminate\Support\Collection|Logics\Flash[] $flash
 * @property \Illuminate\Support\Collection|Logics\Custom[] $custom
 *
 * @property Logics\View $view
 * @property Logics\Redirect $redirect
 *
 * @property \DarkGhostHunter\Larakick\Lexing\Http\Controller $parentController
 * @property string $parentControllerKey
 */
class Action extends Fluent
{
    /**
     * All of the attributes set on the fluent instance.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Create a new fluent instance.
     *
     * @param  array|object  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        parent::__construct(array_merge([
            'route' => new Logics\Route,
        ], $attributes));
    }
}
