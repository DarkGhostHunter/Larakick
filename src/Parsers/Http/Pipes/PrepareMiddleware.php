<?php

namespace DarkGhostHunter\Larakick\Parsing\Http\Pipes;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\Http\Middleware;

class PrepareMiddleware
{
    /**
     * Handle the HTTP scaffold data.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Scaffold $scaffold, Closure $next)
    {
        foreach ($scaffold->rawHttp->get('middleware') as $alias => $data) {
            $scaffold->http->middleware->put($alias, $this->makeMiddleware($alias, $data));
        }

        return $next($scaffold);
    }

    /**
     * Makes a middleware instance.
     *
     * @param  string  $alias
     * @param  array  $data
     * @return \DarkGhostHunter\Larakick\Lexing\Http\Middleware
     */
    protected function makeMiddleware(string $alias, array $data)
    {
        return new Middleware([
            'alias' => $alias,
            'class' => Arr::get($data, 'name', $this->guessClass($alias)),
            'terminable' => Arr::get($data, 'terminable', false)
        ]);
    }

    /**
     * Guesses the Class name for the middleware.
     *
     * @param  string  $alias
     * @return string
     */
    protected function guessClass(string $alias)
    {
        return Str::studly($alias) . 'Middleware';
    }
}
