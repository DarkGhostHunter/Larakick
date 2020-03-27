<?php

namespace DarkGhostHunter\Larakick\Construction\GlobalScope;

use Illuminate\Pipeline\Pipeline;

class GlobalScopeConstructorPipeline extends Pipeline
{
    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [
        Pipes\CreateModelInstance::class,
        Pipes\SetApply::class,
    ];
}
