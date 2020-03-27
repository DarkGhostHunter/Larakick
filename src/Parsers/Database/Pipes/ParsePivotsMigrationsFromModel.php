<?php

namespace DarkGhostHunter\Larakick\Parsing\Database\Pipes;

use Closure;
use LogicException;
use Illuminate\Support\Str;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\Database\Model;
use DarkGhostHunter\Larakick\Lexing\Database\Column;
use DarkGhostHunter\Larakick\Lexing\Database\Relation;
use DarkGhostHunter\Larakick\Lexing\Database\Migration;

class ParsePivotsMigrationsFromModel
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
        // We will cycle through each model and filter any relation that uses a pivot and is not
        // actively using a Pivot Model (which they're already added into the migrations). For
        // each of these relation, we will create a migration guessing the table and columns.
        foreach ($scaffold->database->models as $model) {
            $relations = $model->relations->filter(function (Relation $relation) {
                return ! $relation->usesModelAsPivot();
            });

            foreach ($relations as $relation) {
                $scaffold->database->migrations->push($this->createPivotMigration($model, $relation));
            }
        }

        return $next($scaffold);
    }

    /**
     * Creates a Pivot Migration
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Relation  $relation
     * @return \DarkGhostHunter\Larakick\Lexing\Database\Migration
     */
    protected function createPivotMigration(Model $model, Relation $relation)
    {
        return new Migration([
            'table' => $this->tableName($model, $relation),
            'columns' => $this->getPivotColumns($model, $relation),
            'fromGuessedPivot' => true,
        ]);
    }

    /**
     * Return the pivot table name.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Relation  $relation
     * @return string
     */
    protected function tableName(Model $model, Relation $relation)
    {
        $array = [
            Str::snake($model->class),
            Str::snake($relation->relatedModel->class),
        ];

        sort($array);

        return implode('_', $array);
    }

    /**
     * Return the standard pivot columns.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Relation  $relation
     * @return \Illuminate\Support\Collection
     */
    protected function getPivotColumns(Model $model, Relation $relation)
    {
        // We will guess the pivot columns using the model primary keys. If these are not set,
        // we can't guess what column to use, so in that case we will just bail out from it.
        // In these cases the developer is better by using a primary key or a Pivot Model.
        if (! $relation->relatedModel->primary->using) {
            throw $this->throwNoPrimaryKey($relation->relatedModel);
        }

        if (! $model->primary->using) {
            throw $this->throwNoPrimaryKey($model);
        }

        return collect([
            new Column([
                'name'    => Str::snake($model->class) . '_' . Str::snake($model->primary->column),
                'type'    => Column::realMethod($model->primary->type),
            ]),
            new Column([
                'name'    => Str::snake($relation->relatedModel->class) . '_' . Str::snake($relation->relatedModel->primary->column),
                'type'    => Column::realMethod($relation->relatedModel->primary->type),
            ]),
        ])->sortBy('name');
    }

    /**
     * Throws an exception if the model has no primary key.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @return \LogicException
     */
    protected function throwNoPrimaryKey(Model $model)
    {
        return new LogicException(
            "Create a primary key for [{$model->class}] or create a Pivot model."
        );
    }
}
