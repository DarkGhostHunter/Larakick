<?php

namespace DarkGhostHunter\Larakick\Lexing\Auth;

use Illuminate\Support\Fluent;

/**
 * Class Policy
 *
 * @package DarkGhostHunter\Larakick\Parser\Auth
 *
 * @property string $class
 * @property string $model
 * @property \Illuminate\Support\Collection|string[] $actions
 */
class Policy extends Fluent
{
    /**
     * Actions for policies.
     *
     * @var array
     */
    public const ACTIONS = [
        'index',
        'show',
        'create',
        'update',
        'delete'
    ];

    /**
     * Set the class policy to use all actions.
     *
     * @return void
     */
    public function useAllPolicies()
    {
        $this->actions = collect(self::ACTIONS);
    }

    /**
     * Set the given actions to the policy.
     *
     * @param  array  $actions
     */
    public function setActions(array $actions)
    {
        $this->actions = collect($actions);
    }

}
