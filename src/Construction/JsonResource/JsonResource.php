<?php

namespace DarkGhostHunter\Larakick\Construction\JsonResource;

use Illuminate\Pipeline\Pipeline;

class JsonResource extends Pipeline
{
    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [
        Pipes\CreateModelInstance::class,
        Pipes\SetJsonArray::class
    ];
}
