<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use Illuminate\Support\Str;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use DarkGhostHunter\Larakick\Lexing\Http\Logics\Model;
use DarkGhostHunter\Larakick\Lexing\Database\Relation;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class SetRelations
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
        foreach ($construction->model->relations as $relation) {

            $construction->namespace->addUse($relation->relatedModel->fullNamespace());

            $construction->class->addComment(
                "@property-read {$relation->relatedModel->fullNamespace()} \${$relation->name}"
            );

            $this->setRelation($construction->namespace, $construction->class, $relation)
                ->addComment("@return {$relation->class()}|{$relation->relatedModel->fullNamespace()}");
        }

        return $next($construction);
    }

    /**
     * Sets the relation as a method.
     *
     * @param  \Nette\PhpGenerator\PhpNamespace  $namespace
     * @param  \Nette\PhpGenerator\ClassType  $class
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Relation  $relation
     * @return \Nette\PhpGenerator\Method
     */
    protected function setRelation(PhpNamespace $namespace, ClassType $class, Relation $relation)
    {
        if ($relation->usesPivot()) {
            return $this->setPivotRelation($namespace, $class, $relation);
        }

        if ($relation->throughModel) {
            return $this->setThroughRelation($namespace, $class, $relation);
        }

        return $this->setNormalRelation($class, $relation);
    }

    /**
     * Sets a Pivot relation to the model.
     *
     * @param  \Nette\PhpGenerator\PhpNamespace  $namespace
     * @param  \Nette\PhpGenerator\ClassType  $class
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Relation  $relation
     * @return \Nette\PhpGenerator\Method
     */
    protected function setPivotRelation(PhpNamespace $namespace, ClassType $class, Relation $relation)
    {
        $string = "return $this->{$relation->type}({$relation->relatedModel->class}";

        if ($relation->usesModelAsPivot()) {
            $namespace->addUse($relation->usingPivot->fullNamespace());
            $string .= "->using({$relation->usingPivot->class})";
        }

        if ($relation->withPivotColumns->isNotEmpty()) {
            $string .= '->withPivot(' . implode(',', $relation->withPivotColumns->map(function (string $column) {
                return "'" . $column . "'";
            })) . ')';
        }

        return $class->addMethod($relation->name)
            ->setPublic()
            ->setBody($this->addDefault($relation, $string));
    }

    /**
     * Sets a through relation method body.
     *
     * @param  \Nette\PhpGenerator\PhpNamespace  $namespace
     * @param  \Nette\PhpGenerator\ClassType  $class
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Relation  $relation
     * @return \Nette\PhpGenerator\Method
     */
    protected function setThroughRelation(PhpNamespace $namespace, ClassType $class, Relation $relation)
    {
        $namespace->addUse($relation->throughModel->fullNamespace());

        return $class->addMethod($relation->name)
            ->setPublic()
            ->setBody($this->generateThroughBodyString($relation));
    }

    /**
     * Generate the *Through body name.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Relation  $relation
     * @return string|string[]
     */
    protected function generateThroughBodyString(Relation $relation)
    {
        $string = str_replace(['type', 'related', 'through',], [
            $relation->type,
            $relation->relatedModel->class,
            $relation->throughModel->class,
        ], 'return $type(related,through)');

        return $this->addDefault($relation, $string);
    }

    /**
     * Sets the normal relation.
     *
     * @param  \Nette\PhpGenerator\ClassType  $class
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Relation  $relation
     * @return \Nette\PhpGenerator\Method
     */
    protected function setNormalRelation(ClassType $class, Relation $relation)
    {
        return $class->addMethod($relation->name)
            ->setPublic()
            ->setBody($this->generateBodyString($relation));
    }

    /**
     * Returns the Relation method body string.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Relation  $relation
     * @return string
     */
    protected function generateBodyString(Relation $relation)
    {
        $string = str_replace(['type', 'related'], [
            $relation->type,
            $relation->relatedModel->class,
        ], 'return $type(related)');

        return $this->addDefault($relation, $string);
    }

    /**
     * Adds a default method for a non-existent model record.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Relation  $relation
     * @param  string  $string
     * @return string
     */
    protected function addDefault(Relation $relation, string $string)
    {
        if ($relation->withDefault) {
            $string .= '->withDefault()';
        }

        return Str::finish($string, ';');
    }
}
