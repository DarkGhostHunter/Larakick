<?php

namespace DarkGhostHunter\Larakick\Parsing\Scaffold\Pipes;

use Closure;
use Illuminate\Support\Str;
use DarkGhostHunter\Larakick\Scaffold;
use Illuminate\Contracts\Container\Container;
use DarkGhostHunter\Larakick\Lexing\ScaffoldDatabase;
use DarkGhostHunter\Larakick\Parsing\Http\HttpParserPipeline;

abstract class BaseLexPipe
{
    /**
     * Pipeline to Lex the data.
     *
     * @var string
     */
    protected $pipeline;

    /**
     * Application Service Container.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * LexScaffoldData constructor.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Handle the constructing scaffold data.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  \Closure  $next
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(Scaffold $scaffold, Closure $next)
    {
        $name = $this->getSectionName();

        if ($scaffold->{$name}) {
            $this->lexDataWithPipeline($scaffold);
        }

        return $next($scaffold);
    }

    /**
     * Return the Section name to use
     *
     * @return string
     */
    protected function getSectionName()
    {
        return lcfirst(Str::after(class_basename($this), 'Lex'));
    }

    /**
     * Runs a pipeline to lex the given data from the Scaffold.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function lexDataWithPipeline(Scaffold $scaffold)
    {
        $key = Str::beforeLast($this->getSectionName(), 'Lex');

        $scaffold->{$key} = $this->container->make($this->pipeline)->send(
            $this->makeNewScaffold($scaffold)
        )->thenReturn();
    }

    /**
     * Creates a new Scaffold Section ready to be used to write.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @return \DarkGhostHunter\Larakick\Lexing\ScaffoldDatabase|\DarkGhostHunter\Larakick\Lexing\ScaffoldHttp|\DarkGhostHunter\Larakick\Lexing\ScaffoldAuth
     */
    abstract protected function makeNewScaffold(Scaffold $scaffold);
}
