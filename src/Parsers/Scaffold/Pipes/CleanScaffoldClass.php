<?php

namespace DarkGhostHunter\Larakick\Parsing\Scaffold\Pipes;

use Closure;
use Illuminate\Support\Str;
use DarkGhostHunter\Larakick\Scaffold;

class CleanScaffoldClass
{
    /**
     * Handle the constructing scaffold data.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  \Closure  $next
     *
     * @return mixed
     */
    public function handle(Scaffold $scaffold, Closure $next)
    {
        // Here we will remove the raw data for the parsing for better memory management.
        foreach ($scaffold->getAttributes() as $attribute) {
            if (Str::startsWith($attribute, 'raw')) {
                unset($scaffold[$attribute]);
            }
        }

        return $next($scaffold);
    }
}
