<?php

namespace DarkGhostHunter\Larakick\Construction\Repository;

use Illuminate\Pipeline\Pipeline;

class RepositoryConstructorPipeline extends Pipeline
{
    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [
        Pipes\CreateModelInstance::class,
        Pipes\SetRepositoryMethods::class,
    ];
}
