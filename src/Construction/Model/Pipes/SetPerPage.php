<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class SetPerPage
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
        if ($construction->model->usesNonDefaultPerPage()) {
            $construction->class->addProperty('perPage', $construction->model->perPage)
                ->setProtected()
                ->addComment('The number of models to return for pagination')
                ->addComment("\n")
                ->addComment('@var int');
        }

        return $next($construction);
    }
}
