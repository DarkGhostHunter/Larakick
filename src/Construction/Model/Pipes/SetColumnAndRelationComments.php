<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class SetColumnAndRelationComments
{
    /**
     * Handle the model construction.
     *
     * @param  \DarkGhostHunter\Larakick\Construction\Model\ModelConstruction  $construction
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(ModelConstruction $construction, Closure $next)
    {
        foreach ($construction->model->columns as $column) {
            $construction->class->addComment("@property {$column->phpType()} \${$column->name}");
        }

        foreach ($construction->model->relations as $relation) {
            $construction->class->addComment(
                "@property-read {$relation->relatedModel->fullNamespace()} \${$relation->name}"
            );
        }

        return $next($construction);
    }
}
