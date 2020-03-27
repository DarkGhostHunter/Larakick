<?php

namespace DarkGhostHunter\Larakick\Parsing\Database\Pipes;

use Closure;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\Database\Model;
use DarkGhostHunter\Larakick\Lexing\Database\Column;
use DarkGhostHunter\Larakick\Lexing\Database\Primary;

class ParseModelColumns
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
            foreach ($scaffold->rawDatabase->get("models.{$key}.columns") as $name => $line) {

                if (is_array($line)) {
                    $this->prepareColumnForRelation($name, $model, $line);
                    continue;
                }

                $column = $this->createColumn($name, $line);

                if ($this->modelShouldUsePrimary($model, $column)) {
                    $this->setPrimaryInModel($model, $column);
                }

                $model->columns->put($name, $column);
            }

            $model->columns = $model->columns->filter();
        }

        return $next($scaffold);
    }

    /**
     * Checks if the Column should be treated as a primary key and the Model has not set any primary key yet.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Column  $column
     * @return bool
     */
    protected function modelShouldUsePrimary(Model $model, Column $column)
    {
        return $column->isPrimary() && $model->primary->column === null;
    }

    /**
     * Sets the Primary Key information in the Model instance.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Column  $column
     */
    protected function setPrimaryInModel(Model $model, Column $column)
    {
        $model->primary->using = true;
        $model->primary->column = $column->name;
        $model->primary->type = Column::realMethod($column->type);
        $model->primary->incrementing = Primary::hasIncrementingKey($model->primary->type);
    }

    /**
     * Parses a migration column.
     *
     * @param  string  $name
     * @param  string|array  $data
     * @return \DarkGhostHunter\Larakick\Lexing\Database\Column
     */
    protected function createColumn(string $name, $data)
    {
        return Column::createFromLine($name, $data);
    }

    /**
     * Sets the Column of the model as a relation column to fill later.
     *
     * @param  string  $name
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @param  array  $data
     * @return void
     */
    protected function prepareColumnForRelation(string $name, Model $model, array $data)
    {
        /** @var \DarkGhostHunter\Larakick\Lexing\Database\Relation $relation */
        $relation = $model->relations->get($name);

        if ($relation->needsColumn()) {
            $model->columns->put($name, $column = new Column([
                'name' => $name,
                'relation' => $relation
            ]));

            $model->relations->get($name)->column = $column;
        }
    }
}

