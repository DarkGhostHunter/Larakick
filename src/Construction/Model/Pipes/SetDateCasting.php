<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class SetDateCasting
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
        $dateCastValue = [];

        foreach ($construction->model->columns as $column) {
            if ($column->shouldCastToDate()) {
                $dateCastValue[] = $column->name;
            }
        }

        if (! empty($dateCastValue)) {
            $construction->class->addProperty('dates', $dateCastValue)
                ->addComment('The attributes that should be mutated to dates.')
                ->addComment("\n")
                ->addComment('@var array');
        }

        return $next($construction);
    }
}
