<?php

namespace DarkGhostHunter\Larakick\Parsing\Http\Pipes;

use Closure;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\Http\Action;
use DarkGhostHunter\Larakick\Lexing\Http\Controller;

class ParseControllerActions
{
    /**
     * Handle the controller actions
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Scaffold $scaffold, Closure $next)
    {
        foreach ($scaffold->http->controllers as $key => $controller) {
            foreach ($scaffold->rawHttp->get("controllers.{$key}.actions") as $name => $data) {
                $controller->actions->push(
                    $this->makeAction($name, $key, $controller)
                );
            }
        }

        return $next($scaffold);
    }

    /**
     * Creates a new Action instance.
     *
     * @param  string  $name
     * @param  string  $controllerKey
     * @param  \DarkGhostHunter\Larakick\Lexing\Http\Controller  $controller
     * @return \DarkGhostHunter\Larakick\Lexing\Http\Action
     */
    protected function makeAction(string $name, string $controllerKey, Controller $controller)
    {
        return new Action([
            'name' => $name === 'invoke' ? '__invoke' : $name,
            'parentController' => $controller,
            'parentControllerKey' => $controllerKey,
        ]);
    }
}
