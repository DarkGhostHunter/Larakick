<?php

namespace DarkGhostHunter\Larakick\Parsing\Action\Pipes;

use Closure;
use LogicException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use DarkGhostHunter\Larakick\Lexing\ScaffoldAction;

class CreateSave
{
    /**
     * Handle the controller action.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\ScaffoldAction  $scaffold
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(ScaffoldAction $scaffold, Closure $next)
    {
        if (Arr::has($scaffold->rawAction, 'save')) {

            $save = Arr::get($scaffold->rawAction, 'save');

            $scaffold->action->save->fromFormRequest = (bool)$scaffold->action->formRequest;

            if (is_string($save)) {
                $scaffold->action->save->modelToCreate = $this->getModelToSave(
                    $scaffold->scaffold->database->models, $save
                );
                $scaffold->action->save->variableToSave = $save;
            }

            if (is_array($save)) {
                $scaffold->action->save->variableToSave = array_key_first($save);
                $scaffold->action->save->mergeAttributes = collect(Arr::first($save));
            }
        }

        return $next($scaffold);
    }

    /**
     * Returns the Model to save, if it exists.
     *
     * @param  \Illuminate\Support\Collection  $models
     * @param  string  $save
     * @return null|\DarkGhostHunter\Larakick\Lexing\Database\Model
     */
    protected function getModelToSave(Collection $models, string $save)
    {
        if (ctype_upper($save[0]) && ! $model = $models->get($save)) {
            throw new LogicException("The [$save] Model to create does not exists.");
        }

        return $model ?? null;
    }
}
