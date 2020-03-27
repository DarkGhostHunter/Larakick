<?php

namespace DarkGhostHunter\Larakick\Lexing\Database;

use Illuminate\Support\Fluent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SoftDelete
 *
 * @package DarkGhostHunter\Larakick\Lexing\Database
 *
 * @property bool $using
 * @property string $column
 */
class SoftDelete extends Fluent
{
    /**
     * Default column for soft deletes.
     *
     * @var string
     */
    public const COLUMN = 'deleted_at';

    /**
     * All of the attributes set on the fluent instance.
     *
     * @var array
     */
    protected $attributes = [
        'usesSoftDelete' => false,
        'column' => self::COLUMN,
    ];

    /**
     * Returns if the Soft Delete uses a non default column.
     *
     * @return bool
     */
    public function usesNonDefaultColumn()
    {
        return $this->attributes['column'] !== static::COLUMN;
    }
}
