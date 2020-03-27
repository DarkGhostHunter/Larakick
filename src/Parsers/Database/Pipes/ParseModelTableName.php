<?php

namespace DarkGhostHunter\Larakick\Parsing\Database\Pipes;

use Closure;
use Illuminate\Support\Str;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\Database\Model;

class ParseModelTableName
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
            $model->table = $scaffold->rawDatabase->get("models.{$key}.table");
        }

        return $next($scaffold);
    }
}
