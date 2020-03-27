<?php

namespace DarkGhostHunter\Larakick\Parsing\Http\Pipes;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use DarkGhostHunter\Larakick\Scaffold;
use Illuminate\Contracts\Foundation\Application;
use DarkGhostHunter\Larakick\Lexing\Database\Model;
use DarkGhostHunter\Larakick\Lexing\Http\Controller;

class PrepareController
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * ParseModelsData constructor.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle the HTTP scaffold data.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Scaffold $scaffold, Closure $next)
    {
        $scaffold->http->namespace = data_get($scaffold, 'namespace', $this->app->getNamespace());

        foreach ($scaffold->rawHttp->get('controllers') as $key => $data) {
            $scaffold->http->controllers->put($key, $this->makeController($scaffold, $key, $data));
        }

        return $next($scaffold);
    }

    /**
     * Makes the initial state of the controller.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  string  $key
     * @param  array  $data
     * @return \DarkGhostHunter\Larakick\Lexing\Http\Controller
     */
    protected function makeController(Scaffold $scaffold, string $key, array $data)
    {
        $controller = new Controller([
            'isInvokable' => Arr::get($data, 'invoke', false)
        ]);

        $this->setControllerNamespace($controller, $scaffold->http->namespace, $key);

        return $controller;
    }

    /**
     * Sets the Model Class name and Namespace.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Http\Controller  $controller
     * @param  string  $baseNamespace
     * @param  string  $className
     */
    protected function setControllerNamespace(Controller $controller, string $baseNamespace, string $className)
    {
        $controller->class = Str::camel(Str::afterLast('\\', $className));

        $controller->namespace = $baseNamespace;

        if ($prepend = Str::beforeLast('\\', $className)) {
            $controller->namespace .= '\\' . $prepend;
        }
    }
}
