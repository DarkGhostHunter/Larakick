<?php

namespace DarkGhostHunter\Larakick\Lexing\Database;

use Illuminate\Support\Fluent;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class Relation
 *
 * @package DarkGhostHunter\Larakick\Parser\Eloquent
 *
 * @property string $name
 * @property string $type
 * @property \DarkGhostHunter\Larakick\Lexing\Database\Model $relatedModel
 * @property null|\DarkGhostHunter\Larakick\Lexing\Database\Model $throughModel
 *
 * @property \Illuminate\Support\Collection|\DarkGhostHunter\Larakick\Lexing\Code\Method[] $methods
 *
 * @property \DarkGhostHunter\Larakick\Lexing\Database\Column $belongingColumn
 * @property string $relatedColumn
 *
 * @property \Illuminate\Support\Collection|string[] $withPivotColumns
 *
 * @property bool $withDefault
 *
 * @property null|\DarkGhostHunter\Larakick\Lexing\Database\Model $usingPivot
 */
class Relation extends Fluent
{
    /**
     * List of relations available for Laravel.
     *
     * @var array
     */
    public const RELATIONS = [
        'hasOne',
        'hasOneThrough',
        'hasMany',
        'hasManyThrough',
        'belongsTo',
        'belongsToMany',
        'morphOne',
        'morphMany',
        'morphTo',
        'morphToMany',
        'morphedByMany',
    ];

    public const RELATION_CLASSES = [
        'hasOne'         => HasOne::class,
        'hasOneThrough'  => HasOneThrough::class,
        'hasMany'        => HasMany::class,
        'hasManyThrough' => HasManyThrough::class,
        'belongsTo'      => BelongsTo::class,
        'belongsToMany'  => BelongsToMany::class,
        'morphOne'       => MorphOne::class,
        'morphMany'      => MorphMany::class,
        'morphTo'        => MorphTo::class,
        'morphToMany'    => MorphToMany::class,
        'morphedByMany'  => MorphToMany::class,
    ];

    /**
     * Types of relations that needs a column in the model its declared.
     *
     * @var array
     */
    public const USES_COLUMN = [
        'belongsTo', 'morphsTo',
    ];

    /**
     * Relations that need a Pivot table.
     *
     * @var array
     */
    public const USES_PIVOT = [
        'belongsToMany',
        'morphedByMany',
    ];
    /**
     * All of the attributes set on the fluent instance.
     *
     * @var array
     */
    protected $attributes = [
        'withDefault' => false,
    ];

    /**
     * Returns if the relation type needs a pivot table.
     *
     * @return bool
     */
    public function usesPivot()
    {
        return in_array($this->type, static::USES_PIVOT, true);
    }

    /**
     * Checks if the Relation uses a Model as a Pivot.
     *
     * @return bool
     */
    public function usesModelAsPivot()
    {
        return (bool)$this->usingPivot;
    }

    /**
     * Checks if the Relation needs a Column in the model.
     *
     * @return bool
     */
    public function needsColumn()
    {
        return in_array($this->type, static::USES_COLUMN, true);
    }

    /**
     * Check if the relation doesn't need a Column in the model.
     *
     * @return bool
     */
    public function doesNotNeedsColumn()
    {
        return ! $this->needsColumn();
    }

    /**
     * Returns the class using the relation.
     *
     * @return string
     */
    public function class()
    {
        return self::RELATION_CLASSES[$this->attributes['type']];
    }
}
