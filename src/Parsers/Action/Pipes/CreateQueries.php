<?php

namespace DarkGhostHunter\Larakick\Parsing\Action\Pipes;

use Closure;
use Illuminate\Support\Arr;
use DarkGhostHunter\Larakick\Lexing\Code\Method;
use DarkGhostHunter\Larakick\Lexing\ScaffoldAction;

class CreateQueries
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
        if (Arr::has($scaffold->rawAction, 'queries')) {
            foreach (Arr::get($scaffold->rawAction, 'queries') as $variable => $query) {
                $scaffold->action->queries->put($variable, Method::parseLineWithClass($variable));
            }
        }

        return $next($scaffold);
    }
}
