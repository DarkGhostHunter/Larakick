<?php

namespace DarkGhostHunter\Larakick\Construction\Migration\Pipes;

use Closure;
use DarkGhostHunter\Larakick\Construction\Migration\MigrationConstruction;

class SetDownBlueprint
{
    /**
     * Handle the migration construction.
     *
     * @param  \DarkGhostHunter\Larakick\Construction\Migration\MigrationConstruction  $construction
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(MigrationConstruction $construction, Closure $next)
    {
        $construction->class->addMethod('down')
            ->addComment('Reverse the migrations.')
            ->addComment("\n")
            ->addComment('@return void')
            ->addBody(
                "        Schema::dropIfExists('{$construction->migration->table}');"
            );

        return $next($construction);
    }
}
