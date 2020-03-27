<?php

namespace DarkGhostHunter\Larakick\Parsing\Database\Pipes;

use Closure;
use Illuminate\Support\Arr;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\Database\Model;
use DarkGhostHunter\Larakick\Lexing\Database\Migration;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class ParseModelEvents
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

            $events = $scaffold->rawDatabase->get("models.{$model->dataKey}.events", []);

            $model->observer->merge($this->parseEvents($model, $events));
        }

        return $next($scaffold);
    }

    /**
     * Returns an array of Eloquent Events
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Model  $model
     * @param  array  $events
     * @return \Illuminate\Support\Collection
     */
    protected function parseEvents(Model $model, array $events)
    {
        $observable = (new class extends EloquentModel {})->getObservableEvents();

        // If the array is associative, then we will use the Event name as class.
        if (Arr::isAssoc($events)) {
            return collect($events)->only($observable);
        }

        // If not, we will parse it as {Class}{Action} notation.
        return collect(array_intersect($events, $observable))->mapWithKeys(function ($item, $key) use ($model) {
            return [$key => $model->class . ucfirst($key)];
        });
    }

}
