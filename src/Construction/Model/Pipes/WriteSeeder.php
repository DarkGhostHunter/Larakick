<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use Illuminate\Contracts\Console\Kernel;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class WriteSeeder
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
            $this->console->call('make:seeder', [
                $construction->model->key . 'Seeder' => true,
            ]);
        }

        return $next($construction);
    }
}
