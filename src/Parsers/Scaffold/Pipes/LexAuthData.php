<?php

namespace DarkGhostHunter\Larakick\Parsing\Scaffold\Pipes;

use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\ScaffoldHttp;
use DarkGhostHunter\Larakick\Lexing\ScaffoldAuth;
use DarkGhostHunter\Larakick\Lexing\ScaffoldDatabase;
use DarkGhostHunter\Larakick\Parsing\Http\HttpParserPipeline;
use DarkGhostHunter\Larakick\Parsing\Auth\AuthParserPipeline;
use DarkGhostHunter\Larakick\Parsing\Database\DatabaseParserPipeline;

class LexAuthData extends BaseLexPipe
{
    /**
     * Pipeline to Lex the raw YAML contents.
     *
     * @var string
     */
    protected $pipeline = AuthParserPipeline::class;

    /**
     * @inheritDoc
     */
    protected function makeNewScaffold(Scaffold $scaffold)
    {
        return new ScaffoldAuth([
            'parsed' => $scaffold->rawAuth,
        ]);
    }
}
