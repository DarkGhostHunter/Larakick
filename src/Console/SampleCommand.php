<?php

namespace DarkGhostHunter\Larakick\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use DarkGhostHunter\Larakick\Larakick;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use const DIRECTORY_SEPARATOR;

class SampleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larakick:sample';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Conveniently creates sample YAML files to kick off your project.';

    /**
     * Application Config.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Filesystem implementation.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     */
    public function __construct(Repository $config, Filesystem $filesystem)
    {
        parent::__construct();
        $this->config = $config;
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return mixed|void
     */
    public function handle()
    {
        if ($this->hasScaffoldFiles()) {
            $this->error('Scaffold files already exists!');

            return;
        }

        $this->filesystem->copyDirectory(Larakick::sampleDirectory(), $this->larakickDirectory());

        $this->info('Scaffold files copied! You can start editing them now:');

        foreach (Larakick::FILES as $path) {
            $this->comment($this->larakickDirectory() . DIRECTORY_SEPARATOR . $path);
        }
    }

    /**
     * Detect if the application already has scaffold files.
     *
     * @return bool
     */
    protected function hasScaffoldFiles()
    {
        $files = $this->filesystem->files($this->larakickDirectory());

        foreach ($files as $file) {
            if (in_array($file->getFilename(), Larakick::FILES, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the directory used by Larakick.
     *
     * @return string
     */
    protected function larakickDirectory()
    {
        return Larakick::getBasePath($this->laravel);
    }
}
