<?php

namespace DarkGhostHunter\Larakick\Parsing\Scaffold\Pipes;

use Illuminate\Config\Repository;
use DarkGhostHunter\Larakick\Scaffold;

class ParseDatabaseData extends BaseParserPipe
{
    /**
     * @inheritDoc
     */
    protected function setRepository(Scaffold $scaffold, array $data)
    {
        $repository = new Repository($data);

        $repository->set('models', new Repository($repository->get('models')));
        $repository->set('migrations', new Repository($repository->get('migrations')));

        $scaffold->rawDatabase = $repository;
    }
}
