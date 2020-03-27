<?php

namespace DarkGhostHunter\Larakick\Parsing\Action;

use Illuminate\Pipeline\Pipeline;

class ActionParserPipeline extends Pipeline
{
    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [
        Pipes\CreateModels::class, // Set the Models that will be used as action parameters.
        Pipes\CreateRoute::class, // Add the Route information to the Action
        Pipes\CreateAuthorize::class, //
        Pipes\CreateValidate::class,
        Pipes\CreateQueries::class,
        Pipes\CreateSave::class,
        Pipes\CreateDelete::class,
        Pipes\CreateFire::class,
        Pipes\CreateDispatch::class,
        Pipes\CreateNotify::class,
        Pipes\CreateFlash::class,
        Pipes\CreateCustom::class,
        Pipes\CreateRedirect::class,
        Pipes\CreateView::class,
    ];
}
