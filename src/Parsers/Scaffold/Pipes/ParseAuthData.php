<?php

namespace DarkGhostHunter\Larakick\Parsing\Scaffold\Pipes;

use Closure;
use Illuminate\Config\Repository;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Larakick;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;

class ParseAuthData extends BaseParserPipe
{
    /**
     * @inheritDoc
     */
    protected function setRepository(Scaffold $scaffold, array $data)
    {
        $repository = new Repository($data);

        $repository->set('gates', new Repository($repository->get('gates')));
        $repository->set('policies', new Repository($repository->get('policies')));
        $repository->set('forms', new Repository($repository->get('forms')));

        $scaffold->rawAuth = $repository;
    }
}
