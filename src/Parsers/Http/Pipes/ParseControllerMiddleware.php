<?php

namespace DarkGhostHunter\Larakick\Parsing\Http\Pipes;

use Closure;
use Illuminate\Support\Arr;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\Code\Method;
use DarkGhostHunter\Larakick\Lexing\Http\Controller;

class ParseControllerMiddleware
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
        foreach ($scaffold->rawHttp->get('controllers') as $key => $data) {
            if ($middleware = Arr::get($data, 'middleware')) {
                $this->setControllerMiddleware($scaffold->http->get($key), $middleware);
            }
        }

        return $next($scaffold);
    }

    /**
     * Sets the Controller Middleware
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Http\Controller  $controller
     * @param  array  $middleware
     */
    protected function setControllerMiddleware(Controller $controller, array $middleware)
    {
        $controller->middleware = collect($middleware)->each(function (string $middleware) {
            return Method::parseLine('middleware:'.$middleware);
        });
    }
}
