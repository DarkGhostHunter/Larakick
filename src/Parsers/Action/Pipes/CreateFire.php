<?php

namespace DarkGhostHunter\Larakick\Parsing\Action\Pipes;

use Closure;
use Illuminate\Support\Arr;
use DarkGhostHunter\Larakick\Lexing\Code\Method;
use DarkGhostHunter\Larakick\Lexing\ScaffoldAction;

class CreateFire
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
        if (Arr::has($scaffold->rawAction, 'fire')) {

            $fire = Arr::get($scaffold->rawAction, 'fire');

            if (is_string($fire)) {
                $fire = Arr::wrap($fire);
            }

            foreach ($fire as $event) {
                $scaffold->action->fire->push(Method::parseLineWithClass($event));
            }
        }

        return $next($scaffold);
    }
}
