<?php

namespace DarkGhostHunter\Larakick\Parsing\Database\Pipes;

use Closure;
use LogicException;
use Illuminate\Support\Str;
use Illuminate\Config\Repository;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Larakick;
use Illuminate\Contracts\Foundation\Application;
use DarkGhostHunter\Larakick\Lexing\Database\Model;

class PrepareModelData
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * ParseModelsData constructor.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle the parsing of the Database scaffold.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Scaffold $scaffold, Closure $next)
    {
        $scaffold->database->namespace = data_get($scaffold, 'namespace', $this->app->getNamespace());

        foreach ($scaffold->getRawModels() as $name => $model) {
            $scaffold->database->models->put(
                $name, $this->createModel($scaffold->database->namespace, $name, $model)
            );
        }

        return $next($scaffold);
    }

    /**
     * Creates the Model instance with some basic information.
     *
     * @param  string  $baseNamespace
     * @param  string  $name
     * @param  array  $modelData
     * @return \DarkGhostHunter\Larakick\Lexing\Database\Model
     */
    protected function createModel(string $baseNamespace, string $name, array $modelData)
    {
        $data = new Repository($modelData);

        if (! $data->has('columns')) {
            throw new LogicException("The Model [$name] doesn't have any column set.");
        }

        $model = Model::make();

        $this->setModelClassNamespace($model, $baseNamespace, $name);

        $this->setModelClassExtend($data, $model);

        $this->setModelPerPage($data, $model);

        $this->setModelTargetFile($model);

        return $model;
    }

    /**
     * Sets the Model Class name and Namespace.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @param  string  $baseNamespace
     * @param  string  $className
     */
    protected function setModelClassNamespace(Model $model, string $baseNamespace, string $className)
    {
        $model->key = $className;
        $model->class = Str::camel(Str::afterLast('\\', $className));
        $model->namespace = $baseNamespace;

        if ($prepend = Str::beforeLast('\\', $className)) {
            $model->namespace .= '\\' . $prepend;
        }
    }

    /**
     * Sets the parent Model class.
     *
     * @param  \Illuminate\Config\Repository  $data
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     */
    protected function setModelClassExtend(Repository $data, Model $model)
    {
        $modelClass = Str::lower($data->get('type', 'model'));

        $model->modelType = Model::MODEL_TYPE_MAP[$modelClass];
    }

    /**
     * Set the Model per page records number.
     *
     * @param  \Illuminate\Config\Repository  $data
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     */
    protected function setModelPerPage(Repository $data, Model $model)
    {
        $model->perPage = $data->get('perPage', Model::MODEL_PER_PAGE);
    }

    /**
     * Sets the target file to create for the Model.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     */
    protected function setModelTargetFile(Model $model)
    {
        Larakick::setTargetPath($this->app, $model->fullNamespace());
    }
}
