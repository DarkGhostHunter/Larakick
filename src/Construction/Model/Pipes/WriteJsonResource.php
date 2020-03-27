<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use Illuminate\Support\Facades\File;
use DarkGhostHunter\Larakick\Larakick;
use Illuminate\Contracts\Console\Kernel;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class WriteJsonResource
{
    /**
     * Console.
     *
     * @var \Illuminate\Contracts\Console\Kernel
     */
    protected $console;

    /**
     * WriteModel constructor.
     *
     * @param  \Illuminate\Contracts\Console\Kernel  $console
     */
    public function __construct(Kernel $console)
    {
        $this->console = $console;
    }

    /**
     * Handle the model construction.
     *
     * @param  \DarkGhostHunter\Larakick\Construction\Model\ModelConstruction  $construction
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(ModelConstruction $construction, Closure $next)
    {
        if ($construction->model->useJsonResource) {
            $this->console->call("make:resource {$construction->model->key}");
        }

        return $next($construction);
    }
}
