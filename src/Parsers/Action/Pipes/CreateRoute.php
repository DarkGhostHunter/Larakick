<?php

namespace DarkGhostHunter\Larakick\Parsing\Action\Pipes;

use Closure;
use LogicException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use DarkGhostHunter\Larakick\Lexing\Http\Action;
use DarkGhostHunter\Larakick\Lexing\ScaffoldAction;
use DarkGhostHunter\Larakick\Lexing\Http\Controller;
use DarkGhostHunter\Larakick\Lexing\Http\Logics\Route;

class CreateRoute
{
    /**
     * Handle the controller action.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\ScaffoldAction  $action
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(ScaffoldAction $action, Closure $next)
    {
        if ($route = Arr::get($action->rawAction, 'route')) {
            $this->setRoute($action->action, $route);
        } else {
            $this->guessRoute($action->action, $action->controllerKey);
        }

        return $next($action);
    }

    /**
     * Sets the route based on the raw data.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Http\Action  $action
     * @param  string  $raw
     */
    protected function setRoute(Action $action, string $raw)
    {
        [$verb, $path, $name] = $this->parseRouteParameters($action, $raw);

        $action->route->name = $name;
        $action->route->verb = $this->parseVerb($verb);
        $action->route->path = $path;
    }

    /**
     * Parse the route parameters.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Http\Action  $action
     * @param  string  $raw
     * @return array
     */
    protected function parseRouteParameters(Action $action, string $raw)
    {
        [$route, $name] = explode(' ', $raw);

        $name = $this->normalizeRouteName($action, $name);

        return [Str::before($route, ':'), Str::after($route, ':'), $name];
    }

    /**
     * Normalize the Route name.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Http\Action  $action
     * @param  string|null  $name
     * @return string
     */
    protected function normalizeRouteName(Action $action, string $name = null)
    {
        if ($name) {
            return $name;
        }

        $string = Str::snake(str_replace('Controller', '', $action->parentControllerKey), '.');

        // Add the action name if the action is not invoke.
        return $string . ($action->name === 'invoke' ? '' : '.' . $action->name);
    }

    /**
     * Parses and validates the HTTP verb.
     *
     * @param  string  $verb
     * @return string
     */
    protected function parseVerb(string $verb)
    {
        $verb = strtolower($verb);

        if (! in_array($verb, ['options', 'head', 'get', 'post', 'put', 'patch', 'delete'])) {
            throw new LogicException("The [$verb] verb is not an standard HTTP verb for an action controller.");
        }

        return $verb;
    }

    /**
     * Guesses the Route based on the Controller name and Action name.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Http\Action  $action
     * @param  string  $controllerKey
     */
    protected function guessRoute(Action $action, string $controllerKey)
    {
        $action->route->name = $this->normalizeRouteName($action);
        $action->route->path = $this->guessRoutePath($action, $controllerKey);
        $action->route->verb = Route::actionVerb($action->name);
    }

    /**
     * Guesses the route path for the action.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Http\Action  $action
     * @param  string  $controllerKey
     * @return string
     */
    protected function guessRoutePath(Action $action, string $controllerKey)
    {
        $path = Str::snake($controllerKey, '/');

        if ($action->route->name !== 'invoke') {
            $path .= $action->route->name;
        }

        if ($action->models->isNotEmpty()) {
            $first = $action->models->first();

            $path = Str::finish($path, '/') . $first->getQualifiedPath();

            foreach ($action->models->slice(1) as $model) {
                $path = Str::finish($path, '/') . $model->getDirectoryQualifiedPath();
            }
        }

        return $path;
    }
}
