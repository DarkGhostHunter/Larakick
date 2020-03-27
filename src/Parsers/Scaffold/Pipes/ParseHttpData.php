<?php

namespace DarkGhostHunter\Larakick\Parsing\Scaffold\Pipes;

use Closure;
use Illuminate\Config\Repository;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Larakick;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;

class ParseHttpData extends BaseParserPipe
{
    /**
     * @inheritDoc
     */
    protected function setRepository(Scaffold $scaffold, array $data)
    {
        $repository = new Repository($data);

        $repository->set('controllers', new Repository($repository->get('controllers')));

        $scaffold->rawAuth = $repository;
    }
}
