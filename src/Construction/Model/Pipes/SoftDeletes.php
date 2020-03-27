<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class SoftDeletes
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
        if ($construction->model->softDelete->using) {
            $construction->class->addTrait(\Illuminate\Database\Eloquent\SoftDeletes::class);

            if ($construction->model->softDelete->usesNonDefaultColumn()) {
                $construction->class
                    ->addConstant('DELETED_AT', $construction->model->softDelete->column)
                    ->addComment('The soft delete timestamp column.')
                    ->addComment("\n")
                    ->addComment('@var string');
            }

            $construction->class->addComment(
                "@property-read \Illuminate\Support\Carbon \${$construction->model->softDelete->column}"
            );
        }

        return $next($construction);
    }
}
