<?php

namespace DarkGhostHunter\Larakick\Parsing\Database\Pipes;

use Closure;
use LogicException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\Code\Method;
use DarkGhostHunter\Larakick\Lexing\Database\Column;
use DarkGhostHunter\Larakick\Lexing\Database\Relation;

class ParseModelRelations
{
    /**
     * Handle the parsing of the Database scaffold.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Scaffold $scaffold, Closure $next)
    {
        foreach ($scaffold->database->models as $key => $model) {
            foreach ($scaffold->getRawModel($key, 'columns') as $name => $column) {
                if (is_array($column)) {
                    $model->relations->put($name, $this->parseRelation($name, $column, $scaffold->database->models));
                }
            }
        }

        return $next($scaffold);
    }

    /**
     * Creates the Relation instance from the line.
     *
     * @param  string  $name
     * @param  array  $data
     * @param  \Illuminate\Support\Collection  $models
     * @return \DarkGhostHunter\Larakick\Lexing\Database\Relation
     */
    protected function parseRelation(string $name, array $data, Collection $models)
    {
        if (! $relation = Arr::get($data, 'relation')) {
            throw new LogicException("The [$name] relation has not relation data defined.");
        }

        return $this->createFromColumnData($name, $relation, $models);
    }

    /**
     * Creates a Relation instance from
     *
     * @param  string  $name
     * @param  string  $line
     * @param  \Illuminate\Support\Collection|\DarkGhostHunter\Larakick\Lexing\Database\Model[]  $models
     * @return \DarkGhostHunter\Larakick\Lexing\Database\Relation
     */
    public function createFromColumnData(string $name, string $line, Collection $models)
    {
        $methods = Method::parseLine($line);

        $relation = $methods->shift();

        $this->validateRelation($relation, $models);

        $instance = new Relation([
            'name'         => $name,
            'type'         => $relation->name,
            'relatedModel' => $models->get($relation->arguments->first()),
            'methods'      => $methods,
        ]);

        if ($instance->usesPivot()) {
            $instance->usingPivot = $this->getUsedPivotModel($relation, $models, $methods->firstWhere('name', 'using'));
        }

        if ($include = $methods->firstWhere('withPivot')) {
            $instance->withPivotColumns->push($include);
        }

        if ($instance->needsColumn()) {
            $instance->belongingColumn = new Column([
                'name' => $methods->get('column')
            ]);
        }

        return $instance;
    }

    /**
     * Returns the Model used for Pivot.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Relation  $relation
     * @param  \Illuminate\Support\Collection  $models
     * @param  \DarkGhostHunter\Larakick\Lexing\Code\Method|null  $method
     * @return null|\DarkGhostHunter\Larakick\Lexing\Database\Model
     */
    protected function getUsedPivotModel(Relation $relation, Collection $models, Method $method = null)
    {
        if (! $method) {
            return null;
        }

        /** @var \DarkGhostHunter\Larakick\Lexing\Database\Model $model */
        if (! $model = $models->get($method->arguments->first())) {
            $using = $method->arguments->first();

            throw new LogicException("The [$using] model to use as Pivot doesn't exists.");
        }

        if ($relation->type === 'morphedByMany' && $model->modelType !== 'morphPivot') {
            throw new LogicException("The [{$model->class}] model is not 'morphPivot' type.");
        }

        if ($relation->type === 'belongsToMany' && $model->modelType === 'Pivot') {
            throw new LogicException("The [{$model->class}] model is not 'pivot' type.");
        }

        return $model;
    }

    /**
     * Validate if the relation is valid.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Code\Method  $relation
     * @param  \Illuminate\Support\Collection  $models
     */
    protected function validateRelation(Method $relation, Collection $models)
    {
        if (! $this->isValidRelation($relation->name)) {
            throw new LogicException("The [{$relation->name}] is not a valid relation type.");
        }

        if (! $models->contains('class', $model = $relation->arguments->first())) {
            throw new LogicException("The [{$model}] does not exists in the Models declared.");
        }
    }

    /**
     * Return if the relation issued is in the table of available relations.
     *
     * @param  string  $relation
     * @return bool
     */
    protected function isValidRelation(string $relation)
    {
        return in_array($relation, Relation::RELATIONS, true);
    }
}
