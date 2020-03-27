<?php

namespace DarkGhostHunter\Larakick\Lexing\Database;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Fluent;

/**
 * Class Primary
 *
 * @package DarkGhostHunter\Larakick\Parser\Eloquent
 *
 * @property bool $using
 *
 * @property string $column
 * @property string $type
 * @property bool $incrementing
 */
class Primary extends Fluent
{
    /**
     * Methods that define primary keys.
     *
     * @var array
     */
    public const PRIMARY_COLUMN_METHODS = [
        'id',
        'increments',
        'integerIncrements',
        'tinyIncrements',
        'smallIncrements',
        'mediumIncrements',
        'bigIncrements',
    ];

    /**
     * Blueprint methods that uses incrementing keys
     *
     * @var array
     */
    public const USES_INCREMENTING = [
        'increments',
        'integerIncrements',
        'tinyIncrements',
        'smallIncrements',
        'mediumIncrements',
        'bigIncrements',
    ];

    /**
     * All of the attributes set on the fluent instance.
     *
     * @var array
     */
    protected $attributes = [
        'usesPrimary' => false
    ];

    /**
     * Detect if the primary key uses non default primary key.
     *
     * @return bool
     */
    public function usesNonDefault()
    {
        return $this->attributes['column'] !== 'id'
            && $this->usesIncrementing();
    }

    /**
     * Returns if the Primary uses an incrementing column.
     *
     * @return bool
     */
    public function usesIncrementing()
    {
        return in_array($this->attributes['column'], self::USES_INCREMENTING, true);
    }

    /**
     * Returns if the column definition should be considered a primary key.
     *
     * @param  array  $columnsLines
     * @return bool
     */
    public static function hasColumnWithPrimary(array $columnsLines)
    {
        foreach ($columnsLines as $line) {
            if (Str::contains($line, static::PRIMARY_COLUMN_METHODS)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns if the Column line is using an incrementing column.
     *
     * @param  string  $columnLine
     * @return bool
     */
    public static function hasIncrementingKey(string $columnLine)
    {
        return Str::contains($columnLine, static::USES_INCREMENTING);
    }
}
