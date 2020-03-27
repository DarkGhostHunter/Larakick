<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use Nette\PhpGenerator\PsrPrinter;
use Nette\PhpGenerator\PhpNamespace;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Foundation\Application;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class WriteObserver
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
        if ($construction->model->observer) {
            $this->console->call('make:observer', [
                $construction->model->key . 'Observer' => true,
                '--model' => $construction->model->key
            ]);
        }

        return $next($construction);
    }
}
