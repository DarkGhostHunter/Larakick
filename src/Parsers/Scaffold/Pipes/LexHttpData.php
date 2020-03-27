<?php

namespace DarkGhostHunter\Larakick\Parsing\Scaffold\Pipes;

use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\ScaffoldHttp;
use DarkGhostHunter\Larakick\Lexing\ScaffoldDatabase;
use DarkGhostHunter\Larakick\Parsing\Http\HttpParserPipeline;
use DarkGhostHunter\Larakick\Parsing\Database\DatabaseParserPipeline;

class LexHttpData extends BaseLexPipe
{
    /**
     * Pipeline to Lex the raw YAML contents.
     *
     * @var string
     */
    protected $pipeline = HttpParserPipeline::class;

    /**
     * @inheritDoc
     */
    protected function makeNewScaffold(Scaffold $scaffold)
    {
        return new ScaffoldHttp([
            'parsed' => $scaffold->rawHttp,
        ]);
    }
}
