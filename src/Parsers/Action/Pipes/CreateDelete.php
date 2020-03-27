<?php

namespace DarkGhostHunter\Larakick\Parsing\Action\Pipes;

use Closure;
use Illuminate\Support\Arr;
use DarkGhostHunter\Larakick\Lexing\ScaffoldAction;

class CreateDelete
{
    /**
     * Handle the controller action.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\ScaffoldAction  $scaffold
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(ScaffoldAction $scaffold, Closure $next)
    {
        if (Arr::has($scaffold->rawAction, 'delete')) {
            $scaffold->action->delete->variableToDelete = Arr::get($scaffold->rawAction, 'delete');
        }

        return $next($scaffold);
    }
}
