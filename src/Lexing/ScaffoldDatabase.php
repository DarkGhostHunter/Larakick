<?php

namespace DarkGhostHunter\Larakick\Lexing;

use Illuminate\Support\Fluent;

/**
 * Class ScaffoldDatabase
 *
 * @package DarkGhostHunter\Larakick\Parser
 *
 * @property \Illuminate\Config\Repository $parsed
 *
 * @property string $namespace
 * @property \Illuminate\Support\Collection|\DarkGhostHunter\Larakick\Lexing\Database\Model[] $models
 * @property \Illuminate\Support\Collection|\DarkGhostHunter\Larakick\Lexing\Database\Migration[] $migrations
 */
class ScaffoldDatabase extends Fluent
{

}
