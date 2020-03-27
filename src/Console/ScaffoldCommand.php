<?php

namespace DarkGhostHunter\Larakick\Console;

use Illuminate\Console\Command;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Writer\WriterPipeline;
use DarkGhostHunter\Larakick\Parsing\Scaffold\ScaffoldParserPipeline;

class ScaffoldCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larakick:scaffold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold your next big idea.';

    /**
     * @var \DarkGhostHunter\Larakick\Parsing\Scaffold\ScaffoldParserPipeline
     */
    protected $scaffold;

    /**
     * @var \DarkGhostHunter\Larakick\Writer\WriterPipeline
     */
    protected $writer;

    /**
     * Create a new command instance.
     *
     * @param  \DarkGhostHunter\Larakick\Parsing\Scaffold\ScaffoldParserPipeline  $scaffold
     * @param  \DarkGhostHunter\Larakick\Writer\WriterPipeline  $writer
     */
    public function __construct(ScaffoldParserPipeline $scaffold, WriterPipeline $writer)
    {
        parent::__construct();

        $this->scaffold = $scaffold;
        $this->writer = $writer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('[0/2] Starting to parse your project...');

        $scaffold = $this->scaffold->send(new Scaffold())->thenReturn();

        $this->info('[1/2]  Parsers done. Now we will overwrite your files.');

        $this->writer->send($scaffold)->thenReturn();

        $this->info('[2/2] Writing done.');

        $this->info('Your scaffold is ready. Happy coding!');
    }
}
