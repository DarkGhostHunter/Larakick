<?php

namespace DarkGhostHunter\Larakick;

use Symfony\Component\Yaml\Yaml;

class Lexer
{
    public function parseYaml(string $path)
    {
        Yaml::parse($path);
    }
}
