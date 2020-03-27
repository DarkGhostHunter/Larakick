<?php

namespace DarkGhostHunter\Larakick\Lexing\Http\Logics;

use Illuminate\Support\Str;
use Illuminate\Support\Fluent;

/**
 * Class Model
 *
 * @package DarkGhostHunter\Larakick\Parser\Http\Logics
 *
 * @property string $variable  The variable name to use inside the Action
 * @property \DarkGhostHunter\Larakick\Lexing\Database\Model $model  The actual Model instance used as the parameter.
 * @property null|string $routeBind  If the model should be route bound to the model column
 */
class Model extends Fluent
{
    /**
     * Returns the Qualified Path for the route, like "{model:bind}"
     *
     * @return string
     */
    public function getQualifiedPath()
    {
        $path = "{{$this->variable}";

        if ($this->routeBind) {
            $path .= ":{$this->routeBind}";
        }

        return "{$path}}";
    }

    /**
     * Returns the Directory Qualified Path for the Route, like "model/{model:bind}".
     *
     * @return string
     */
    public function getDirectoryQualifiedPath()
    {
        return Str::camel($this->model->class) . '/' . $this->getQualifiedPath();
    }
}
