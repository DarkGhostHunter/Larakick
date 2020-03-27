<?php

namespace DarkGhostHunter\Larakick\Parsing\Database;

use Illuminate\Pipeline\Pipeline;

class DatabaseParserPipeline extends Pipeline
{
    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [
        Pipes\PrepareModelData::class, // Creates a "Model" class for each model
        Pipes\ParseModelTableName::class, // Gets the table name for each model
        Pipes\ParseModelRelations::class, // Parses each column set as a relation
        Pipes\ParseModelColumns::class, // Parses the real columns of each model.
        Pipes\ParseModelPrimaryKey::class, // Overwrites the primary key information.
        Pipes\ParseModelBelongingColumns::class, // Fills  columns needed for belonging relation.
        Pipes\ParseMigrationFromModel::class, // Creates a migration for each model.
        Pipes\ParsePivotsMigrationsFromModel::class, // Adds non-resolved pivot tables.
        Pipes\ParseModelFillable::class, // Set each model fillable properties.
        Pipes\ParseModelEvents::class, // Set model eloquent events.
        Pipes\ParseModelRouteBinding::class, // Set model column to use as route binding.
        Pipes\ParseMigrations::class, // Parse all the migrations from the raw data.
        Pipes\ParseGlobalScopes::class, // Add the Global Scopes to the model.
        Pipes\ParseJsonResources::class, // Add the JSON Resources to the model.
        Pipes\ParseRepositories::class, // Add the Repositories model.
    ];
}
