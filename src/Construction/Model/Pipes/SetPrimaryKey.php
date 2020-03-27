<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use Nette\PhpGenerator\Property;
use Nette\PhpGenerator\ClassType;
use DarkGhostHunter\Larakick\Lexing\Database\Model;
use DarkGhostHunter\Larakick\Lexing\Database\Column;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class SetPrimaryKey
{
    /**
     * Handle the model construction.
     *
     * @param  \DarkGhostHunter\Larakick\Construction\Model\ModelConstruction  $construction
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(ModelConstruction $construction, Closure $next)
    {
        if (! $construction->model->primary->using) {
            $this->unsetPrimaryKey($construction->class);
        } elseif ($construction->model->primary->usesNonDefault()) {
            $this->setNonDefaultPrimaryKey($construction->model, $construction->class);
        }

        return $next($construction);
    }

    /**
     * Unset the Primary Key information.
     *
     * @param  \Nette\PhpGenerator\ClassType  $class
     */
    protected function unsetPrimaryKey(ClassType $class)
    {
        $this->setPrimaryKeyProperty($class);
        $this->setIncrementingProperty($class);
    }

    /**
     * Sets the primary key property value.
     *
     * @param  \Nette\PhpGenerator\ClassType  $class
     * @param  null  $value
     */
    protected function setPrimaryKeyProperty(ClassType $class, $value = null)
    {
        $class->addProperty('primaryKey', $value)
            ->addComment('The primary key for the model.')
            ->addComment("\n")
            ->addComment('@var string');
    }

    /**
     * Sets the incrementing property value.
     *
     * @param  \Nette\PhpGenerator\ClassType  $class
     * @param  bool  $incrementing
     */
    protected function setIncrementingProperty(ClassType $class, bool $incrementing = false)
    {
        $class->addProperty('incrementing', $incrementing)
            ->addComment('Indicates if the IDs are auto-incrementing.')
            ->addComment("\n")
            ->addComment('@var bool');
    }

    /**
     * Sets a non-default primary key.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @param  \Nette\PhpGenerator\ClassType  $class
     */
    protected function setNonDefaultPrimaryKey(Model $model, ClassType $class)
    {
        $this->setPrimaryKeyProperty($class, $model->primary->column);
        $this->setIncrementingProperty($class, $model->primary->incrementing);

        if (($type = Column::getPhpType($model->primary->column)) !== 'int') {
            $class->addProperty('keyType', $type)
                ->addComment('The "type" of the primary key ID.')
                ->addComment("\n")
                ->addComment('@var string');
        }
    }
}
