<?php

namespace DarkGhostHunter\Larakick\Parsing\Action\Pipes;

use Closure;
use Illuminate\Support\Arr;
use DarkGhostHunter\Larakick\Lexing\Http\Action;
use DarkGhostHunter\Larakick\Lexing\ScaffoldAction;

class CreateAuthorize
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
        if (Arr::has($scaffold->rawAction, 'authorize')) {
            $this->setAuthorization($scaffold, $scaffold->action, Arr::get($scaffold->rawAction, 'authorize'));
        }

        return $next($scaffold);
    }

    /**
     * Sets the authorization
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\ScaffoldAction  $scaffold
     * @param  \DarkGhostHunter\Larakick\Lexing\Http\Action  $action
     * @param  null  $authorize
     */
    protected function setAuthorization(ScaffoldAction $scaffold, Action $action, $authorize = null)
    {
        // Don't do any authorization if it's manually disabled (false).
        if ($authorize === false) {
            $action->authorize->using = false;
            return;
        }

        // From there we will use authorization will be used if the key is present. If the developer
        // has an "empty" authorization, we will guess the models or it class name, or leave it to
        // a Model Policy to update the parameters automatically in the Authentication pipeline.
        $action->authorize->using = true;

        if ($authorize === null && $action->models->isNotEmpty()) {
            $action->authorize->parameters->push($action->models->map->variable);

            return;
        }

        $parameters = collect(explode(' ', $authorize));

        // Check if the second parameter is uppercase (Model name) and remove it from the parameters.
        if (ctype_upper($parameters->get(1)[0])) {
            $action->authorize->parameters->push(
                $scaffold->scaffold->database->models->get($parameters->shift())->fullNamespace()
            );
        }

        $action->authorize->parameters->push($parameters);
    }
}
