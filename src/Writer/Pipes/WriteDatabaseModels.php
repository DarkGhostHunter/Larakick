<?php

namespace DarkGhostHunter\Larakick\Writer\Pipes;

use Closure;
use Nette\PhpGenerator\ClassType;
use DarkGhostHunter\Larakick\Scaffold;
use Illuminate\Database\Eloquent\SoftDeletes;
use DarkGhostHunter\Larakick\Lexing\Database\Model;

class WriteDatabaseModels
{
    /**
     * Handle writing the scaffold files.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  \Closure  $next
     *
     * @return mixed
     */
    public function handle(Scaffold $scaffold, Closure $next)
    {
        foreach ($scaffold->database->models as $model) {
            $this->createModel($scaffold, $model);
        }

        return $next($scaffold);
    }

    protected function createModel(Scaffold $scaffold, Model $model)
    {

    }
}
