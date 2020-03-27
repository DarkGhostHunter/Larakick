<?php

namespace DarkGhostHunter\Larakick\Parsing\Action\Pipes;

use Closure;
use Illuminate\Support\Arr;
use DarkGhostHunter\Larakick\Lexing\Code\Method;
use DarkGhostHunter\Larakick\Lexing\Http\Action;
use DarkGhostHunter\Larakick\Lexing\ScaffoldAction;
use DarkGhostHunter\Larakick\Lexing\Http\Logics\Validate;

class CreateValidate
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
        if (Arr::has($scaffold->rawAction, 'validate')) {
            $this->setValidation($scaffold->action, Arr::get($scaffold->rawAction, 'validate'));
        }

        return $next($scaffold);
    }

    /**
     * For each validation line, put it on the validation collection.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Http\Action  $action
     * @param  array  $validate
     */
    protected function setValidation(Action $action, array $validate)
    {
        foreach ($validate as $property => $validation) {

            if (is_string($validation) && ctype_upper($validation[0])) {
                $action->validate->put($property, new Validate([
                    'formRequest' => $validation
                ]));
                continue;
            }

            $action->validate->put($property, new Validate([
                'property' => $property,
                'rules' => $validation
            ]));
        }
    }
}
