<?php

namespace DarkGhostHunter\Larakick\Parsing\Database\Pipes;

use Closure;
use LogicException;
use Illuminate\Support\Arr;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\Database\Model;

class ParseModelPrimaryKey
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

            /** @var array $data */
            $data = $scaffold->getRawModel($key);

            if ($this->modelShouldNotUsePrimary($data)) {
                $this->disablePrimary($model);
                continue;
            }

            if ($this->hasPrimaryFilled($key, $data)) {
                $this->fillPrimary($data, $model);
                continue;
            }

            $model->primary->using = false;
        }

        return $next($scaffold);
    }

    /**
     * Returns if the model should not use any primary key.
     *
     * @param  array  $data
     * @return bool
     */
    protected function modelShouldNotUsePrimary(array $data)
    {
        return Arr::get($data, 'primary') === false;
    }

    /**
     * Disable the primary key for the Model.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     */
    protected function disablePrimary(Model $model)
    {
        $model->primary->using = false;
    }

    /**
     * Checks if the Primary key has been properly filled.
     *
     * @param  string  $name
     * @param  array  $data
     * @return bool
     */
    protected function hasPrimaryFilled(string $name, array $data)
    {
        if (! Arr::has($data, ['primary'])) {
            return false;
        }

        if (! Arr::has($data, ['primary.column', 'primary.type', 'primary.incrementing'])) {
            throw new LogicException("The {$name} primary data must have column, type and incrementing.");
        }

        if (! Arr::has(Arr::get($data, 'columns'), $column = Arr::get($data, 'primary.column'))) {
            throw new LogicException("The {$column} of {$name} as primary is not in the list of columns");
        }

        return true;
    }

    /**
     * Fill the primary key with the data supplied.
     *
     * @param  array  $data
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     */
    protected function fillPrimary(array $data, Model $model)
    {
        $model->primary->using = true;
        $model->primary->column = Arr::get($data, 'primary.column');
        $model->primary->type = Arr::get($data, 'primary.type');
        $model->primary->incrementing = Arr::get($data, 'primary.incrementing');
    }
}
