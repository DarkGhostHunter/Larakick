<?php

namespace DarkGhostHunter\Larakick\Construction\Seeder;

use Illuminate\Pipeline\Pipeline;

class SeederConstructorPipeline extends Pipeline
{
    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [
        Pipes\CreateModelInstance::class,
        Pipes\SetSeeder::class,
    ];
}
