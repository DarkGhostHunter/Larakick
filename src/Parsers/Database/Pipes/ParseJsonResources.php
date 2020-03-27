<?php

namespace DarkGhostHunter\Larakick\Parsing\Database\Pipes;

use Closure;
use DarkGhostHunter\Larakick\Scaffold;

class ParseJsonResources
{
    /**
     * Handle the parsing of the Database scaffold.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Scaffold $scaffold, Closure $next)
    {
        foreach ($scaffold->database->models as $key => $model) {
            $model->useJsonResource = $scaffold->rawDatabase->get("models.{$key}.json", false);
        }

        return $next($scaffold);
    }
}
