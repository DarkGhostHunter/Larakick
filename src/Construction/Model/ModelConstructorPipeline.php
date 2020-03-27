<?php

namespace DarkGhostHunter\Larakick\Construction\Model;

use Illuminate\Pipeline\Pipeline;

class ModelConstructorPipeline extends Pipeline
{
    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [
        Pipes\CreateModelInstance::class,
        Pipes\SetTableName::class,
        Pipes\SetPerPage::class,
        Pipes\SetColumnCasting::class,
        Pipes\SetDateCasting::class,
        Pipes\SetFillable::class,
        Pipes\SetRelations::class,
        Pipes\SetPrimaryKey::class,
        Pipes\SetRouteBinding::class,
        Pipes\SetColumnAndRelationComments::class,
        Pipes\SetTimestamp::class,
        Pipes\SoftDeletes::class,
        Pipes\WriteModel::class,
        Pipes\WriteObserver::class,
        Pipes\WriteJsonResource::class,
        Pipes\WriteRepository::class,
        Pipes\WriteGlobalScopes::class,
        Pipes\WriteFactory::class,
        Pipes\WriteSeeder::class,
    ];
}
