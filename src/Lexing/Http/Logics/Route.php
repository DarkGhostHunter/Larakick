<?php

namespace DarkGhostHunter\Larakick\Lexing\Http\Logics;

use Illuminate\Support\Fluent;

/**
 * Class Route
 *
 * @package DarkGhostHunter\Larakick\Parser\Http\Logics
 *
 * @property string $verb Name of the HTTP Verb
 * @property string $path String for to route the request.
 * @property string $action Class{at}Action notation to put in the route.
 * @property string $name Name of the route.
 */
class Route extends Fluent
{
    /**
     * HTTP Verbs for controllers.
     *
     * @var array
     */
    public const VERB_MAP = [
        'index'   => 'GET',
        'create'  => 'GET',
        'store'   => 'POST',
        'show'    => 'GET',
        'edit'    => 'GET',
        'update'  => ['PUT', 'PATCH'],
        'destroy' => 'DELETE',
    ];

    /**
     * Resource actions.
     *
     * @var array
     */
    public const RESOURCE_ACTIONS = [
        'index',
        'create',
        'show',
        'update',
        'delete',
    ];

    /**
     * Action verb for the its name, if available. Defaults to "get".
     *
     * @param  string  $name
     * @return mixed|string
     */
    public static function actionVerb(string $name)
    {
        return self::VERB_MAP[$name] ?? 'get';
    }
}
