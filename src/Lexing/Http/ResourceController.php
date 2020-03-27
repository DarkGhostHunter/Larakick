<?php

namespace DarkGhostHunter\Larakick\Lexing\Http;

use Illuminate\Support\Fluent;

/**
 * Class ResourceController
 *
 * @package DarkGhostHunter\Larakick\Parser\Http
 *
 * @property bool $using
 *
 * @property bool $isJsonResource
 *
 * @property \Illuminate\Support\Collection|\DarkGhostHunter\Larakick\Lexing\Http\Logics\Model[] $resourceModels
 * @property array|string[] $only
 * @property array|string[] $except
 * @property bool $isApi
 */
class ResourceController extends Fluent
{
    /**
     * All of the attributes set on the fluent instance.
     *
     * @var array
     */
    protected $attributes = [
        'using' => false,
        'isJsonResource' => false,
        'only' => [],
        'except' => [],
        'isApi' => false,
    ];

    /**
     * Returns if the Resource Controller uses all actions.
     *
     * @return bool
     */
    public function usesAllActions()
    {
        return empty($this->attributes['only']) && empty($this->attributes['except']);
    }
}
