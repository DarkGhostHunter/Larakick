<?php

namespace DarkGhostHunter\Larakick\Parsing\Action\Pipes;

use Closure;
use LogicException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use DarkGhostHunter\Larakick\Lexing\ScaffoldAction;
use DarkGhostHunter\Larakick\Lexing\Http\Logics\Model;

class CreateModels
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
        $models = Arr::get($scaffold->rawAction, 'models');

        $scaffold->action->models->push(
            $this->parseLines($models, $scaffold->scaffold->database->models)
        );

        return $next($scaffold);
    }

    /**
     * Parses a line string for models.
     *
     * @param  string|array  $lines
     * @param  \Illuminate\Support\Collection  $models
     * @return array
     */
    protected function parseLines($lines, Collection $models)
    {
        if (is_string($lines)) {
            $lines = explode(' ', $lines);
        }

        $instances = [];

        foreach ($lines as $variable => $line) {

            if (! $instance = $models->get($name = Str::before($line, ':'))) {
                throw new LogicException("The [{$name}] model for the route doesn't exists in the Models.");
            }

            $instances[] = new Model([
                'routeBind' => Str::contains($line, ':') ? Str::after($line, ':') : null,
                'model'     => $instance,
                'variable'  => is_int($variable) ? Str::camel($name) : $variable,
            ]);
        }

        return $instances;
    }
}
