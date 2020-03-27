<?php

namespace DarkGhostHunter\Larakick\Parsing\Http\Pipes;

use Closure;
use Illuminate\Config\Repository;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\Http\Action;
use DarkGhostHunter\Larakick\Lexing\ScaffoldAction;
use DarkGhostHunter\Larakick\Parsing\Action\ActionParserPipeline;

class SendActionsToPipeline
{
    /**
     * Pipeline for Actions.
     *
     * @var \DarkGhostHunter\Larakick\Parsing\Action\ActionParserPipeline
     */
    protected $pipeline;

    /**
     * ParseControllerActions constructor.
     *
     * @param  \DarkGhostHunter\Larakick\Parsing\Action\ActionParserPipeline  $pipeline
     */
    public function __construct(ActionParserPipeline $pipeline)
    {
        $this->pipeline = $pipeline;
    }

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
            foreach ($controller->actions as $action) {
                $this->pipeline->send(new ScaffoldAction([
                    'scaffold' => $scaffold,
                    'action' => $action,
                    'controllerKey' => $key,
                    'rawAction' => $scaffold->rawHttp->get("controllers.{$key}.actions.{$action->name}")
                ]))->thenReturn();
            }
        }

        return $next($scaffold);
    }
}
