<?php

namespace DarkGhostHunter\Larakick\Lexing\Database;

use LogicException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;
use DarkGhostHunter\Larakick\Lexing\Code\Method;
use Illuminate\Support\Collection as BaseCollection;

/**
 * Class Column
 *
 * @package DarkGhostHunter\Larakick\Parser\Eloquent
 *
 * @property string $name
 * @property string $type
 * @property \Illuminate\Support\Collection|\DarkGhostHunter\Larakick\Lexing\Code\Method[] $methods
 * @property \DarkGhostHunter\Larakick\Lexing\Database\Relation $relation
 */
class Column extends Fluent
{
    /**
     * List of real integer methods behind the incrementing methods.
     *
     * @var array
     */
    public const INCREMENTING_TO_INTEGER = [
        'increments'        => 'unsignedInteger',
        'integerIncrements' => 'unsignedInteger',
        'tinyIncrements'    => 'unsignedTinyInteger',
        'smallIncrements'   => 'unsignedSmallInteger',
        'mediumIncrements'  => 'unsignedMediumInteger',
        'bigIncrements'     => 'unsignedBigInteger',
    ];

    /**
     * Maps blueprint methods to the PHP type
     *
     * @var array
     */
    public const BLUEPRINT_TO_TYPE = [
        'int' => [
            'id',
            'increments',
            'integerIncrements',
            'tinyIncrements',
            'smallIncrements',
            'mediumIncrements',
            'bigIncrements',
            'integer',
            'unsignedInteger',
            'unsignedTinyInteger',
            'unsignedSmallInteger',
            'unsignedMediumInteger',
            'unsignedBigInteger',
        ],
        'float' => [
            'decimal',
            'double',
            'float',
            'point',
        ],
        'bool' => [
            'bool',
            'boolean',
        ],
        'array' => [
            'json',
            'jsonb'
        ],
        \Illuminate\Support\Carbon::class => [
            'date',
            'dateTime',
            'dateTimeTz',
            'time',
            'timeTz',
            'timestamp',
            'timestampTz',
            'year',
        ],
    ];

    /**
     * Maps the blueprint to Eloquent casting array. Strings are default null.
     *
     * @var array
     */
    public const BLUEPRINT_TO_CAST = [
        'integer' => [
            'id',
            'increments',
            'integerIncrements',
            'tinyIncrements',
            'smallIncrements',
            'mediumIncrements',
            'bigIncrements',
            'integer',
            'unsignedInteger',
            'unsignedTinyInteger',
            'unsignedSmallInteger',
            'unsignedMediumInteger',
            'unsignedBigInteger',
        ],
        'float' => [
            'decimal',
            'double',
            'float',
            'point',
        ],
        'bool' => [
            'bool',
            'boolean',
        ],
        'array' => [
            'json',
            'jsonb'
        ],
    ];

    /**
     * Map of dates that should be casted
     *
     * @var array
     */
    public const BLUEPRINT_TO_DATES = [
        'date',
        'dateTime',
        'dateTimeTz',
        'time',
        'timeTz',
        'timestamp',
        'timestampTz',
        'year',
    ];

    /**
     * Create a new fluent instance.
     *
     * @param  array|object  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        $this->methods = collect();
    }

    /**
     * Returns if the Column should be considered as primary key.
     *
     * @return bool
     */
    public function isPrimary()
    {
        return in_array($this->type, Primary::PRIMARY_COLUMN_METHODS, true);
    }

    /**
     * Checks if the Column is needed for a "belonging" relation.
     *
     * @return bool
     */
    public function isRelationColumn()
    {
        return (bool) $this->relation;
    }

    /**
     * Returns the PHP type of the column.
     *
     * @return string
     */
    public function phpType()
    {
        return static::getPhpType($this->attributes['type']);
    }

    /**
     * Returns the PHP variable type for a column type.
     *
     * @param  string  $type
     * @return mixed|string
     */
    public static function getPhpType(string $type)
    {
        return static::BLUEPRINT_TO_TYPE[$type] ?? 'string';
    }

    /**
     * Returns the Eloquent Cast type, if any.
     *
     * @return null|string
     */
    public function castType()
    {
        return static::BLUEPRINT_TO_CAST[$this->attributes['type']] ?? null;
    }

    /**
     * Checks if it should be mutated to date.
     *
     * @return bool
     */
    public function shouldCastToDate()
    {
        return isset(self::BLUEPRINT_TO_DATES[$this->attributes['type']]);
    }

    /**
     * Return if a model exists in the given collection of models.
     *
     * @param  \Illuminate\Support\Collection|\DarkGhostHunter\Larakick\Lexing\Database\Model[]  $models
     * @param  string  $model
     * @return bool
     */
    public static function relationModelExists(Collection $models, string $model)
    {
        return $models->contains('class', $model);
    }

    /**
     * Guess and creates a Column instance based on a given Relation.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Relation  $relation
     * @param  \Illuminate\Support\Collection  $models
     * @return \DarkGhostHunter\Larakick\Lexing\Database\Column
     */
    public static function guessRelationColumn(Relation $relation, Collection $models)
    {
        /** @var \DarkGhostHunter\Larakick\Lexing\Database\Model $model */
        if (! $model = $models->firstWhere('class', $relation->relatedModel)) {
            throw new LogicException("The model for the {$relation->name} doesn't exists.");
        }

        $column = new static([
            'isRelation' => true,
            'methods'    => collect(),
        ]);

        // If the mode is using primary column, we will use that information to locate the column.
        if ($primary = $model->primary->column) {
            $column->name = Str::snake($model->class) . '_' . $primary;
            $column->type = $model->columns->firstWhere('name', $primary)->type;
        }
        else {
            $column->name = Str::snake($model->class) . '_' . $relation->name;
            $column->type = $model->columns->firstWhere('name', $relation->name)->type;
        }

        return $column;
    }

    /**
     * Creates a Column instance from a name and line.
     *
     * @param  string  $name
     * @param  string  $columnLine
     * @return static
     */
    public static function createFromLine(string $name, string $columnLine)
    {
        $arguments = Method::parseLine($columnLine);

        return new static([
            'name'    => $name,
            'type'    => $arguments->shift(),
            'methods' => $arguments,
        ]);
    }

    /**
     * Returns the real method for an incrementing key.
     *
     * @param  string  $type
     * @return string
     */
    public static function realMethod(string $type)
    {
        return Arr::get(static::INCREMENTING_TO_INTEGER, $type, $type);
    }
}
