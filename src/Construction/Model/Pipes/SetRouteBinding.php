<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class SetRouteBinding
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
        if ($construction->model->routeBinding) {
            $construction->class->addMethod('getKeyName')
                ->addBody("return \${$construction->model->routeBinding};")
                ->addComment('Get the primary key for the model.')
                ->addComment("\n")
                ->addComment('@return string');
        }

        return $next($construction);
    }
}
