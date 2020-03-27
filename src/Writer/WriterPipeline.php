<?php

namespace DarkGhostHunter\Larakick\Writer;

use Illuminate\Pipeline\Pipeline;

class WriterPipeline extends Pipeline
{
    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [
        Pipes\WriteDatabaseModels::class,
        Pipes\WriteDatabaseMigrations::class,
        Pipes\WriteDatabaseGlobalScopes::class,
        Pipes\WriteDatabaseJsonResources::class,
        Pipes\WriteDatabaseResources::class,

        Pipes\WriteHttpMiddleware::class,
        Pipes\WriteHttpKernelMiddleware::class,
        Pipes\WriteHttpControllers::class,

        Pipes\WriteAuthGates::class,
        Pipes\WriteAuthPolicies::class,
        Pipes\WriteRequestForms::class,
    ];
}
