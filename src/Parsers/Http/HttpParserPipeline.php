<?php

namespace DarkGhostHunter\Larakick\Parsing\Http;

use Illuminate\Pipeline\Pipeline;

class HttpParserPipeline extends Pipeline
{
    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [
        Pipes\PrepareMiddleware::class,
        Pipes\PrepareController::class,
        Pipes\ParseControllerResource::class, // Mark Controllers that must be resourceful.
        Pipes\ParseControllerMiddleware::class,
        Pipes\ParseControllerActions::class,
        Pipes\SendActionsToPipeline::class,
    ];
}
