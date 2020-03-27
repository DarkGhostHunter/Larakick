<?php

namespace DarkGhostHunter\Larakick\Parsing\Scaffold\Pipes;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Larakick;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;

abstract class BaseParserPipe
{
    /**
     * Application.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Filsystem.
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * ParseYamlFileToScaffoldDatabase constructor.
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
     * Handle the constructing scaffold data.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  \Closure  $next
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(Scaffold $scaffold, Closure $next)
    {
        if ($path = $this->getFilePathIfExists()) {
            $this->setRepository($scaffold, $this->getFileContents($path));
        }

        return $next($scaffold);
    }

    /**
     * Return the file key to parse.
     *
     * @return null|string
     */
    protected function getFilePathIfExists()
    {
        $key = Str::lower(Str::between(class_basename($this), 'Parse', 'Data'));

        $files = Larakick::getFilePathsFor($key);

        foreach ($files as $path) {

            $realPath = $this->getYamlRealPath($path);

            if ($this->yamlFileExists($realPath)) {
                return $realPath;
            }
        }

        return null;
    }

    /**
     * Returns the YAML real file path in the system.
     *
     * @param  string  $file
     * @return string
     */
    public function getYamlRealPath(string $file)
    {
        return $this->app->basePath($file);
    }

    /**
     * Determines if the YAML file exists.
     *
     * @param  string  $path
     * @return bool
     */
    protected function yamlFileExists(string $path)
    {
        return $this->filesystem->exists($path);
    }

    /**
     * Get the file contents of the YAML file.
     *
     * @param  string  $path
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getFileContents(string $path)
    {
        return Yaml::parse($this->filesystem->get($path), 1024 + 2048);
    }

    /**
     * Sets the raw data into the Repository.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  array  $data
     */
    abstract protected function setRepository(Scaffold $scaffold, array $data);
}
