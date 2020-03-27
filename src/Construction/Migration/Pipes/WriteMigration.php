<?php

namespace DarkGhostHunter\Larakick\Construction\Migration\Pipes;

use Closure;
use Nette\PhpGenerator\PsrPrinter;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use DarkGhostHunter\Larakick\Construction\Migration\MigrationConstruction;

class WriteMigration
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
     * Handle the migration construction.
     *
     * @param  \DarkGhostHunter\Larakick\Construction\Migration\MigrationConstruction  $construction
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(MigrationConstruction $construction, Closure $next)
    {
        $this->filesystem->put(
            $this->app->databasePath('migrations' . \DIRECTORY_SEPARATOR . $construction->migration->filename()),
            $this->printer->printNamespace($construction->namespace)
        );

        return $next($construction);
    }
}
