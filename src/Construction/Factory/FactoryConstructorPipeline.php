<?php

namespace DarkGhostHunter\Larakick\Construction\Factory;

use Illuminate\Pipeline\Pipeline;

class FactoryConstructorPipeline extends Pipeline
{
    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [
        Pipes\CreateModelInstance::class,
        Pipes\SetFactoryAttributes::class,
        Pipes\SetStates::class,
    ];
}
