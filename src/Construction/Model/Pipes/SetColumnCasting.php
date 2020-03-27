<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class SetColumnCasting
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
        $castValue = [];

        foreach ($construction->model->columns as $column) {
            $castValue[$column->name] = $column->castType();
        }

        $castValue = array_filter($castValue ?? []);

        if (! empty($castValue)) {
            $construction->class->addProperty('casts', $castValue)
                ->addComment('The attributes that should be cast.')
                ->addComment("\n")
                ->addComment('@var.');
        }

        return $next($construction);
    }
}
