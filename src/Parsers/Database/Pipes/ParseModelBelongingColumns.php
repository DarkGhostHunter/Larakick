<?php

namespace DarkGhostHunter\Larakick\Parsing\Database\Pipes;

use Closure;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\Database\Column;
use DarkGhostHunter\Larakick\Lexing\Database\Relation;

class ParseModelBelongingColumns
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
        foreach ($scaffold->database->models as $model) {
            foreach ($model->relations->filter->needsColumn() as $relation) {
                $this->fillRelationColumn($relation);
            }
        }

        return $next($scaffold);
    }

    /**
     * Fills the column data using the relation data.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Relation  $relation
     */
    protected function fillRelationColumn(Relation $relation)
    {
        $column = $this->getColumnFromTargetModel($relation);

        $relation->belongingColumn->type = Column::realMethod($column->type);
        $relation->belongingColumn->name = $this->guessColumnName($relation);
    }

    /**
     * Returns the type of the column.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Relation  $relation
     * @return \DarkGhostHunter\Larakick\Lexing\Database\Column
     */
    protected function getColumnFromTargetModel(Relation $relation)
    {
        return $relation->relatedModel->columns->get($relation->name);
    }

    /**
     * Guess the Column name using the primary key of the model.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Relation  $relation
     * @return string
     */
    protected function guessColumnName(Relation $relation)
    {
        // First, we will use the column name the user issued in the YAML, that is stored here.
        if ($relation->belongingColumn->name) {
            return $relation->belongingColumn->name;
        }

        // If not, we will try to guess the name of the primary key of the model it belongs to.
        if ($relation->relatedModel->primary->using) {
            return $relation->relatedModel->singular() . '_' . $relation->relatedModel->primary->column;
        }

        // We don't have nor the name or primary key, so we will blindly guess the name as "{name}_id".
        return $relation->name . '_id';
    }
}
