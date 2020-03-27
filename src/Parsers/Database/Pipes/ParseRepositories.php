<?php

namespace DarkGhostHunter\Larakick\Parsing\Database\Pipes;

use Closure;
use DarkGhostHunter\Larakick\Scaffold;

class ParseRepositories
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
            $model->useRepository = $scaffold->rawDatabase->get("models.{$key}.repository", false);
        }

        return $next($scaffold);
    }
}
