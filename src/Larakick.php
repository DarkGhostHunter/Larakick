<?php

namespace DarkGhostHunter\Larakick;

use LogicException;
use Illuminate\Support\Str;
use Illuminate\Contracts\Foundation\Application;
use const DIRECTORY_SEPARATOR;

class Larakick
{
    /**
     * PHP Stub directories.
     *
     * @var string
     */
    public const STUB_DIR = __DIR__ . '/../stubs/DummyRepository.stub';

    /**
     * Files valid for scaffolding, in order of precedence.
     *
     * @var array
     */
    public const FILES = [
        'database' => ['database', 'db', 'models'],
        'http'     => ['http', 'controllers'],
        'auth'     => ['auth', 'authentication', 'authorization'],
    ];

    /**
     * Valid extensions for YAML files.
     *
     * @var array
     */
    public const EXTENSIONS = ['yml', 'yaml'];

    /**
     * Path of Larakick files from the project's base path.
     *
     * @var string
     */
    public const PATH = 'larakick';

    /**
     * Returns file paths that Larakick uses for scaffolding for a given section.
     *
     * @param  string  $section
     * @return array
     */
    public static function getFilePathsFor(string $section)
    {
        if (! isset(static::FILES[$section])) {
            throw new LogicException("The [$section] section is not available to scaffold in Larakick");
        }

        return collect(static::FILES[$section])
            ->crossJoin(static::EXTENSIONS)
            ->map(function ($path) {
                return self::makePath([
                    static::PATH, implode('.', $path),
                ]);
            })->all();
    }

    /**
     * Returns the Larakick scaffold files path.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $application
     * @return string
     */
    public static function getBasePath(Application $application)
    {
        return $application->basePath() . DIRECTORY_SEPARATOR . static::PATH;
    }

    /**
     * Returns the samples directory path.
     *
     * @return string
     */
    public static function sampleDirectory()
    {
        return __DIR__ . static::makePath(['..', 'samples']);
    }

    /**
     * Returns the Target path for a given class.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  string  $fullNamespace
     * @return string
     */
    public static function setTargetPath(Application $app, $fullNamespace)
    {
        return static::makePath([
            $app->basePath(),
            Str::snake(Str::before($fullNamespace, '\\')),
            str_replace('\\', DIRECTORY_SEPARATOR, Str::between($fullNamespace, '\\', '\\')),
            Str::afterLast($fullNamespace, '\\') . '.php',
        ]);
    }

    /**
     * Makes a valid local path from an array.
     *
     * @param  array  $directories
     * @return string
     */
    public static function makePath(array $directories)
    {
        return implode(DIRECTORY_SEPARATOR, array_map(function ($directories) {
            return trim($directories, ' \\/');
        }, $directories));
    }
}
