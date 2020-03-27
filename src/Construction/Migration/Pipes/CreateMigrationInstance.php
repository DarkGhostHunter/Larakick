<?php

namespace DarkGhostHunter\Larakick\Construction\Migration\Pipes;

use Closure;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use DarkGhostHunter\Larakick\Construction\Migration\MigrationConstruction;

class CreateMigrationInstance
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
        $construction->class = new ClassType($construction->migration->className());
        $construction->class->setExtends(Migration::class);

        $construction->namespace = new PhpNamespace('');
        $construction->namespace->addClass($construction->class);
        $construction->namespace->addUse(Schema::class);
        $construction->namespace->addUse(Blueprint::class);


        return $next($construction);
    }
}
