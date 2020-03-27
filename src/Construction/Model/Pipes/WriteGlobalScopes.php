<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use Illuminate\Support\Str;
use DarkGhostHunter\Larakick\Larakick;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use DarkGhostHunter\Larakick\Lexing\Database\Model;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class WriteGlobalScopes
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
        foreach ($construction->model->globalScopes as $scope) {
            $this->filesystem->put(
                $this->getScopePath($construction->model),
                $this->getReplacedStubContents($scope, $construction->model)
            );
        }

        return $next($construction);
    }

    /**
     * Returns the final repository path.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @return string
     */
    protected function getScopePath(Model $model)
    {
        $path = implode(DIRECTORY_SEPARATOR, [
            'App', 'Repositories', Str::finish($model->key, 'Scope') . '.php',
        ]);

        return $this->app->basePath($path);
    }

    /**
     * Replaces the stub contents with the model class name and namespace.
     *
     * @param  string  $name
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @return string|string[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getReplacedStubContents(string $name, Model $model)
    {
        $contents = $this->filesystem->get(Larakick::STUB_DIR . '/DummyScope.stub');

        return str_replace([
            'DummyNamespace',
            'DummyModel',
            'DummyScope',
            'DummyModelNamespace',
        ], [
            $this->app->getNamespace() . '\Scopes',
            $model->class,
            $name,
            $model->fullNamespace(),
        ], $contents);
    }
}
