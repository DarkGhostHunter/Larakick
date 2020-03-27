<?php

namespace DarkGhostHunter\Larakick\Construction\Model\Pipes;

use Closure;
use Nette\PhpGenerator\ClassType;
use DarkGhostHunter\Larakick\Lexing\Database\Timestamps;
use DarkGhostHunter\Larakick\Construction\Model\ModelConstruction;

class SetTimestamp
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
        if ($construction->model->timestamps->using) {
            $this->setTimestamps($construction->model->timestamps, $construction->class);
        } else {
            $construction->class->addProperty('timestamps', false)
                ->setPublic()
                ->addComment('Indicates if the model should be timestamped.')
                ->addComment("\n")
                ->addComment('@var bool');
        }

        return $next($construction);
    }

    /**
     * Set the timestamps for the model.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Timestamps  $timestamps
     * @param  \Nette\PhpGenerator\ClassType  $class
     */
    protected function setTimestamps(Timestamps $timestamps, ClassType $class)
    {
        if ($timestamps->createdAtColumn) {
            $class->addConstant('UPDATED_AT', $timestamps->createdAtColumn)
                ->setPublic()
                ->addComment('The "created at" column name.')
                ->addComment("\n")
                ->addComment('@var string');
        }
        if ($timestamps->updatedAtColumn) {
            $class->addConstant('CREATED_AT', $timestamps->createdAtColumn)
                ->setPublic()
                ->addComment('The "updated at" column name.')
                ->addComment("\n")
                ->addComment('@var string');
        }

        $class->addComment("@property-read \Illuminate\Support\Carbon \${$timestamps->createdAtColumn}");
        $class->addComment("@property-read \Illuminate\Support\Carbon \${$timestamps->updatedAtColumn}");
    }
}
