<?php

namespace DarkGhostHunter\Larakick\Lexing\Database;

use Illuminate\Support\Fluent;

/**
 * Class Timestamps
 *
 * @package DarkGhostHunter\Larakick\Parser\Eloquent
 *
 * @property bool $using
 * @property null|string $createdAtColumn
 * @property null|string $updatedAtColumn
 */
class Timestamps extends Fluent
{


    /**
     * Create a new Primary instance.
     *
     * @return static
     */
    public static function make()
    {
        return new static([
            'usesTimestamps' => true
        ]);
    }
}
