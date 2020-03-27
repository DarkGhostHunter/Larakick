<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class SetFillable
{
    /**
     * Handle the model construction
     *
     * @param  \DarkGhostHunter\Larakick\Construction\Model\ModelConstruction  $construction
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(ModelConstruction $construction, Closure $next)
    {
        if ($construction->model->fillable->isNotEmpty()) {
            $construction->class->addProperty('fillable', $construction->model->fillable->all())
                ->addComment('The attributes that are mass assignable.')
                ->addComment("\n")
                ->addComment('@var array');
        }

        return $next($construction);
    }
}
