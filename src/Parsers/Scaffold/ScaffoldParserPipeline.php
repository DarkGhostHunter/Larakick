<?php

namespace DarkGhostHunter\Larakick\Parsing\Scaffold;

use Illuminate\Pipeline\Pipeline;

class ScaffoldParserPipeline extends Pipeline
{
    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [
        Pipes\ParseDatabaseData::class,
        Pipes\ParseHttpData::class,
        Pipes\ParseAuthData::class,
        Pipes\LexDatabaseData::class,
        Pipes\LexHttpData::class,
        Pipes\LexDatabaseData::class,
        Pipes\CleanScaffoldClass::class
    ];
}
