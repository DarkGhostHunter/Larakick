<?php

namespace DarkGhostHunter\Larakick\Parsing\Database\Pipes;

use Closure;
use Illuminate\Support\Arr;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\Code\Method;
use DarkGhostHunter\Larakick\Lexing\Database\Model;
use DarkGhostHunter\Larakick\Lexing\Database\GlobalScope;

class ParseGlobalScopes
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
        foreach ($scaffold->database->models as $model) {
            if ($scopes = $scaffold->rawDatabase->get("models.{$model->dataKey}.scopes")) {
                foreach ($scopes as $scope) {
                    $model->globalScopes->push($scope);
                }
            }
        }

        return $next($scaffold);
    }
}
