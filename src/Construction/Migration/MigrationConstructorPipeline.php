<?php

namespace DarkGhostHunter\Larakick\Construction\Migration;

use Illuminate\Pipeline\Pipeline;

class MigrationConstructorPipeline extends Pipeline
{
    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [
        Pipes\CreateMigrationInstance::class,
        Pipes\SetUpBlueprint::class,
        Pipes\SetDownBlueprint::class,
        Pipes\WriteMigration::class
    ];
}
