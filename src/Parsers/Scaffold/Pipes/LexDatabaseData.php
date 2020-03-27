<?php

namespace DarkGhostHunter\Larakick\Parsing\Scaffold\Pipes;

use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\ScaffoldDatabase;
use DarkGhostHunter\Larakick\Parsing\Database\DatabaseParserPipeline;

class LexDatabaseData extends BaseLexPipe
{
    /**
     * Pipeline to Lex the raw YAML contents.
     *
     * @var string
     */
    protected $pipeline = DatabaseParserPipeline::class;

    /**
     * @inheritDoc
     */
    protected function makeNewScaffold(Scaffold $scaffold)
    {
        return new ScaffoldDatabase([
            'parsed' => $scaffold->rawDatabase,
        ]);
    }
}
