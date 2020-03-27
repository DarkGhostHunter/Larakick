<?php

namespace DarkGhostHunter\Larakick\Lexing\Database;

use Illuminate\Support\Str;
use Illuminate\Support\Fluent;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use const DIRECTORY_SEPARATOR;

/**
 * Class Model
 *
 * @package DarkGhostHunter\Larakick\Parser
 *
 * @property string $key The key name for the collection.
 *
 * @property string $class  The final name of the Model class.
 * @property string $namespace  The namespace of the Model.
 *
 * @property string $modelType  The parent class type, that could be "Model", "Pivot" or "MorphPivot".
 * @property int $perPage  How many models per page should be set.
 * @property int $seed  The number of models to seed, outside the default.
 *
 * @property null|string $table  The table name, in case it's not the default.
 * @property \Illuminate\Support\Collection|\DarkGhostHunter\Larakick\Lexing\Database\Column[] $columns
 * @property \Illuminate\Support\Collection|string[] $fillable
 * @property \Illuminate\Support\Collection|\DarkGhostHunter\Larakick\Lexing\Database\Relation[] $relations
 *
 * @property \DarkGhostHunter\Larakick\Lexing\Database\Primary $primary  Primary column information
 * @property \DarkGhostHunter\Larakick\Lexing\Database\Timestamps $timestamps  Timestamps information.
 * @property \DarkGhostHunter\Larakick\Lexing\Database\SoftDelete $softDelete  Soft Deleting information.
 *
 * @property null|string $routeBinding  Column that should be bound by default, it any.
 * @property bool $useFactory  If a factory should be created for it.
 *
 * @property bool $useJsonResource
 * @property bool $useRepository
 * @property \Illuminate\Support\Collection|string[] $globalScopes
 * @property bool $observer
 */
class Model extends Fluent
{
    /**
     * Model types.
     *
     * @var array
     */
    public const MODEL_TYPE_MAP = [
        'model'      => \Illuminate\Database\Eloquent\Model::class,
        'pivot'      => Pivot::class,
        'morphPivot' => MorphPivot::class,
    ];

    /**
     * Default number of models to retrieve for a page.
     *
     * @var int
     */
    public const MODEL_PER_PAGE = 15;

    /**
     * Returns if the Model is a Pivot model.
     *
     * @return bool
     */
    public function isPivot()
    {
        return in_array($this->modelType, [
            'pivot'      => Pivot::class,
            'morphPivot' => MorphPivot::class,
        ], true);
    }

    /**
     * Return the Model full namespace.
     *
     * @return string
     */
    public function fullNamespace()
    {
        return trim($this->namespace, '\\') . '\\' . trim($this->class, '\\');
    }

    /**
     * Return the namespace after the "app" directory.
     *
     * @return string
     */
    public function appNamespace()
    {
        return Str::after($this->fullNamespace(), 'App\\');
    }

    /**
     * Returns the snake case singular name of the model.
     *
     * @return string
     */
    public function singular()
    {
        return Str::snake($this->class);
    }

    /**
     * Returns if the model uses a non default per-page value.
     *
     * @return bool
     */
    public function usesNonDefaultPerPage()
    {
        return $this->attributes['perPage'] !== static::MODEL_PER_PAGE;
    }

    /**
     * Returns if it's using a custom table name.
     *
     * @return bool
     */
    public function usesNonDefaultTable()
    {
        return $this->table !== null;
    }

    /**
     * Guesses the table for the given Model.
     *
     * @return string
     */
    public function guessTable()
    {
        return Str::snake(Str::pluralStudly($this->attributes['class']));
    }

    /**
     * Returns the target file for the model class.
     *
     * @return string
     */
    public function targetFile()
    {
        return ucfirst(str_replace('\\', DIRECTORY_SEPARATOR, $this->fullNamespace())) . '.php';
    }

    /**
     * Create a new Model instance.
     *
     * @return static
     */
    public static function make()
    {
        return new static([
            'modelType'        => static::MODEL_TYPE_MAP['model'],
            'perPage'          => static::MODEL_PER_PAGE,
            'columns'          => collect(),
            'fillable'         => collect(),
            'relations'        => collect(),
            'primary'          => new Primary,
            'timestamps'       => new Timestamps,
            'softDelete'       => new SoftDelete,
            'routeBinding'     => null,
            'factory'          => true,
            'seed'             => static::MODEL_PER_PAGE,
            'usesJsonResource' => false,
            'usesRepository'   => false,
            'globalScopes'     => collect(),
            'events'           => false,
        ]);
    }
}
