<?php

namespace DarkGhostHunter\Larakick\Parsing\Auth;

use Illuminate\Pipeline\Pipeline;

class AuthParserPipeline extends Pipeline
{
    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [
        Pipes\CreateGates::class,
        Pipes\CreatePolicies::class,
        Pipes\CreateFormRequests::class,
    ];
}
