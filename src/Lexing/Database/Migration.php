<?php

namespace DarkGhostHunter\Larakick\Lexing\Database;

use Illuminate\Support\Str;
use Illuminate\Support\Fluent;

/**
 * Class Migration
 *
 * @package DarkGhostHunter\Larakick\Parser\Eloquent
 *
 * @property string $table
 * @property \Illuminate\Support\Collection|\DarkGhostHunter\Larakick\Lexing\Database\Column[] $columns
 * @property string $primary
 * @property array $indexes
 *
 * @property bool $fromGuessedPivot
 */
class Migration extends Fluent
{
    /**
     * All of the attributes set on the fluent instance.
     *
     * @var array
     */
    protected $attributes = [
        'fromGuessedPivot' => false,
    ];

    /**
     * Returns the filename of the migration.
     *
     * @return string
     */
    public function filename()
    {
        return now()->format('Y_m_d_His') . '_' . Str::snake($this->className());
    }

    /**
     * Returns the class name of the migration.
     *
     * @return string
     */
    public function className()
    {
        return 'Create' . Str::studly($this->table) . 'Table';
    }
}
