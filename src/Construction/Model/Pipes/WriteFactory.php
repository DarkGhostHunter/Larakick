<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use DarkGhostHunter\Larakick\Lexing\Database\Model;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;
use const DIRECTORY_SEPARATOR;

class WriteFactory
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
     */
    public function handle(ModelConstruction $construction, Closure $next)
    {
        if ($construction->model->useFactory && $construction->model->columns->isNotEmpty()) {
            $this->filesystem->put(
                $this->getFactoryPath($construction->model),
                $this->createFactoryFile($construction->model)
            );
        }

        return $next($construction);
    }

    /**
     * Returns the path for the Factory file.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @return string
     */
    protected function getFactoryPath(Model $model)
    {
        return $this->app->databasePath('factories' . DIRECTORY_SEPARATOR . $model->key . 'Factory.php');
    }

    /**
     * Creates the Factory file contents
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @return string
     */
    protected function createFactoryFile(Model $model)
    {
        $string = '<?php' . "\n" . "use {$model->fullNamespace()};\n\n" .
            "\$factory->define({$model->class}::class, function (Faker \$faker) {" .
            '    return [' . "\n";

        $columns = $model->primary->usesIncrementing()
            ? $model->columns->except($model->primary->column)
            : $model->columns;

        foreach ($columns as $column) {
            $string .= "        '{$column->name}' => \$faker->{$column->name},\n";
        }

        return $string . '    ];';
    }
}
