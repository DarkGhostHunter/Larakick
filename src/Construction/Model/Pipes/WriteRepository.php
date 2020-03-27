<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use Illuminate\Support\Str;
use DarkGhostHunter\Larakick\Larakick;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use DarkGhostHunter\Larakick\Lexing\Database\Model;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;
use const DIRECTORY_SEPARATOR;

class WriteRepository
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Application Filesystem.
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Creates a new WriteRepository instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Contracts\Filesystem\Filesystem  $filesystem
     */
    public function __construct(Application $app, Filesystem $filesystem)
    {
        $this->app = $app;
        $this->filesystem = $filesystem;
    }

    /**
     * Handle the model construction.
     *
     * @param  \DarkGhostHunter\Larakick\Construction\Model\ModelConstruction  $construction
     * @param  \Closure  $next
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(ModelConstruction $construction, Closure $next)
    {
        $this->filesystem->put(
            $this->getRepositoryPath($construction->model),
            $this->getReplacedStubContents($construction->model)
        );

        return $next($construction);
    }

    /**
     * Returns the final repository path.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @return string
     */
    protected function getRepositoryPath(Model $model)
    {
        $path = implode(DIRECTORY_SEPARATOR, [
            'App', 'Repositories', Str::finish($model->key, 'Repository') . '.php',
        ]);

        return $this->app->basePath($path);
    }

    /**
     * Replaces the stub contents with the model class name and namespace.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @return string|string[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getReplacedStubContents(Model $model)
    {
        $contents = $this->filesystem->get(Larakick::STUB_DIR . '/DummyRepository.stub');

        return str_replace([
            'DummyNamespace',
            'DummyModel',
            'dummyModel',
            'DummyModelNamespace',
        ], [
            $this->app->getNamespace() . '\Scopes',
            $model->class,
            Str::camel($model->class),
            $model->fullNamespace(),
        ], $contents);
    }
}
