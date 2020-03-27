<?php

namespace DarkGhostHunter\Larakick\Parsing\Database\Pipes;

use Closure;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\Database\Migration;

class ParseMigrationFromModel
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
            $scaffold->database->migrations->put(
                $model->table,
                new Migration([
                    'table' => $model->table,
                    'columns' => $model->columns
                ])
            );
        }

        return $next($scaffold);
    }
}
