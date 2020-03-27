<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use Nette\PhpGenerator\PsrPrinter;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class WriteModel
{
    /**
     * PHP PSR Printer
     *
     * @var \Nette\PhpGenerator\PsrPrinter
     */
    protected $printer;

    /**
     * Application filesystem.
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * WriteModel constructor.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Contracts\Filesystem\Filesystem  $filesystem
     * @param  \Nette\PhpGenerator\PsrPrinter  $printer
     */
    public function __construct(Application $app, Filesystem $filesystem, PsrPrinter $printer)
    {
        $this->app = $app;
        $this->filesystem = $filesystem;
        $this->printer = $printer;
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
        $this->filesystem->put(
            $this->app->basePath($construction->model->targetFile()),
            $this->printer->printClass($construction->class)
        );

        return $next($construction);
    }
}
