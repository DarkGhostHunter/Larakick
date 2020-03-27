<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use DarkGhostHunter\Larakick\Lexing\Database\Model;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class CreateModelInstance
{
    /**
     * Handle the model construction
     *
     * @param  \DarkGhostHunter\Larakick\Construction\Model\ModelConstruction  $construction
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(ModelConstruction $construction, Closure $next)
    {
        $construction->class = new ClassType($construction->model->class);
        $construction->class->setExtends($construction->model->modelType);

        $construction->namespace = new PhpNamespace($construction->model->namespace);
        $construction->namespace->addClass($construction->class);

        $this->setBuilderPhpDocs($construction->class, $construction->model);

        return $next($construction);
    }

    /**
     * Set the Eloquent Builder methods to document the return of the model.
     *
     * @param  \Nette\PhpGenerator\ClassType  $class
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     */
    protected function setBuilderPhpDocs(ClassType $class, Model $model)
    {
        $class->addComment('@mixin \Illuminate\Database\Eloquent\Builder');

        $methods = [
            'make(array $attributes = [])',
            'create(array $attributes = [])',
            'forceCreate(array $attributes)',
            'firstOrNew(array $attributes = [], array $values = [])',
            'firstOrFail($columns = [\'*\'])',
            'firstOrCreate(array $attributes, array $values = [])',
            'firstOr($columns = [\'*\'], Closure $callback = null)',
            'firstWhere($column, $operator = null, $value = null, $boolean = \'and\')',
            'updateOrCreate(array $attributes, array $values = [])',
            'findOrFail($id, $columns = [\'*\'])',
            'findOrNew($id, $columns = [\'*\'])',
        ];

        foreach ($methods as $method) {
            $class->addComment("@method {$model->fullNamespace()} $method");
        }

        $methods = [
            'first($columns = [\'*\'])',
            'find($id, $columns = [\'*\'])'
        ];

        foreach ($methods as $method) {
            $class->addComment("@method null|{$model->fullNamespace()} $method");
        }
    }
}
