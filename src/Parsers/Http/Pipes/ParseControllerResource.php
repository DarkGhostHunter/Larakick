<?php

namespace DarkGhostHunter\Larakick\Parsing\Http\Pipes;

use Closure;
use LogicException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\Http\ResourceController;

class ParseControllerResource
{
    /**
     * Handle the HTTP scaffold data.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Scaffold $scaffold, Closure $next)
    {
        foreach ($scaffold->rawHttp->get('controllers') as $key => $data) {
            if ($resource = Arr::get($data, 'resource')) {
                $this->fillResource($scaffold, $scaffold->http->controllers->get($key)->resource, $key, $resource);
            }
        }

        return $next($scaffold);
    }

    /**
     * Returns the Resource Controller instance.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  \DarkGhostHunter\Larakick\Lexing\Http\ResourceController  $resource
     * @param  string  $key
     * @param  array  $data
     * @return void
     */
    protected function fillResource(Scaffold $scaffold, ResourceController $resource, string $key, array $data)
    {
        $resource->using = true;

        $resource->isJsonResource = Arr::get($data, 'json', false);

        $resource->resourceModels = $this->getResourceModels($scaffold, $key, $data, $resource->isJsonResource);

        if (Arr::has($data, 'except')) {
            $resource->except = explode(' ', Arr::get($data, 'except'));
        }
        elseif (Arr::has($data, 'only')) {
            $resource->only = explode(' ', Arr::get($data, 'only'));
        }

        $resource->isApi = $resource->isJsonResource ?: Arr::get($data, 'api', false);
    }

    /**
     * Return the Models that this Resource controller makes.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  string  $key
     * @param  array  $data
     * @param  bool  $onlyFirst
     * @return \Illuminate\Support\Collection
     */
    protected function getResourceModels(Scaffold $scaffold, string $key, array $data, bool $onlyFirst = false)
    {
        $keys = $this->guessModel($scaffold, $key, $data);

        if ($onlyFirst) {
            $keys = Arr::first($keys);
        }

        $models = [];

        foreach ($keys as $modelKey) {
            if (! $model = $scaffold->database->models->get($modelKey)) {
                throw new LogicException("The [{$modelKey}] model doesnt exists for this [{$key}].");
            }

            $models[$modelKey] = $model;
        }

        return collect($models);
    }

    /**
     * Guesses the model name.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  string  $key
     * @param  array  $data
     * @return array
     */
    protected function guessModel(Scaffold $scaffold, string $key, array $data)
    {
        return explode(' ', Arr::get($data, 'models', Str::before($key, 'Controller')));
    }
}
