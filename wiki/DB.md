# Database

Larakick makes setting your database a breeze, avoiding rounds of syncing your models with your migrations, factories, seeders, and most importantly, relations. 

Models in Larakick are defined in `larakick/db.yml`. Creating a model will spawn their migration, factory and seeder automatically. You don't have to do nothing.

The schema is relatively basic:

```yaml
namespace: App

models:
  User:
    columns: 
      id: ~
      name: string
      email: string unique
      email_verified_at: timestamp nullable
      password: string
      rememberToken: ~ 
      timestamps: ~

  Post:
    columns:
      id: ~
      uuid: uuid
      title: string
      excerpt: string nullable
      body: text
      author: belongsTo:User
      published_at: timestampTz
      timestampsTz: ~
    
  Image:
    columns:
      id: ~
      path: string
      imageable: morphsTo
      timestampsTz: ~

migrations:
  failed_jobs:
    id: ~
    connection: text
    queue: text
    payload: longText
    exception: longText
    failed_at: timestamp useCurrent
```

> To set a null value into a key you can use `~`, which is semantically preferred over `null`.

## Namespace

The namespace of all the Models is set in the `namespace` key says. Every model will prepend that namespace, an be created under PSR-4 standard.

You can change this to your convenience.

```yaml
namespace: Any\Namespace\You\Want
```

## Models

Models are named by the key name in singular. Migrations are created using using studly case on plural.

For example, `GameLeaderboard` will make a table called `game_leaderboards`.

```yaml
models:
  GameLeaderboard:
    # ...
```

```php
<?php

class GameLeaderboard extends Model
{
    // ...
}
```

```php
Schema::create('game_leaderboards', function (Blueprint $table) {
    // ...
});
```

You can also append the model name to a namespace:

```yaml
namespace: App\Models

models:
  Leaderboards\Moba:
    # ...
```

The above will spawn:

```php
<?php

namespace App\Models\Leaderboards;

use Illuminate\Database\Eloquent\Model;

class Moba extends Model
{
    // ...
}
```

When creating a model, the following logic will be automated for you:

* [Casting attributes](https://laravel.com/docs/7.x/eloquent-mutators#attribute-casting) to their appropriate type (strings, integers, floats... ).
* [Mutating dates](https://laravel.com/docs/7.x/eloquent-mutators#date-mutators) columns as `date` and `datetime`.
* PHPDoc mixin for Eloquent Builder, `create` and `make` methods among others.
* PHPDoc blocks for model properties and relations.

For example, this `Podcast` model will create the following model:

```yaml
namespace: App\Models

models:
  Podcast:
    columns: 
      uuid: ~
      show:
        column: show_uuid
        relation: belongsTo:Show withDefault
      subscribers:
        relation: hasMany:User
      name: string
      slug: string
      length: int
      published_at: timestamp nullable
      timestamps: ~
      softDeletes: ~
    perPage: 20
    primary:
      column: slug
      keyType: string
      incrementing: false
    fillable:
      - name
      - slug
      - length

  # ...
```

The resulting model will be this:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Podcast
 *
 * @package App\Models
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @method static App\Models\Podcast create($attributes = [])
 * @method static App\Models\Podcast make($attributes = [])
 * @method static App\Models\Podcast firstOrCreate(array $attributes, array $values = [])
 * @method static App\Models\Podcast firstOrNew(array $attributes = [], array $values = [])
 * @method static App\Models\Podcast firstOr($columns = ['*'], Closure $callback = null)
 * @method static App\Models\Podcast firstWhere($column, $operator = null, $value = null, $boolean = 'and')
 * @method static App\Models\Podcast updateOrCreate(array $attributes, array $values = [])
 * 
 * @property-read \App\Models\Show $show
 * @property-read \Illuminate\Database\Eloquent\Collection $listeners
 *  
 * @property-read string $uuid
 * @property string $show_uuid
 * @property string $name
 * @property string $slug
 * @property int $length
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Podcast extends Model
{
    use SoftDeletes;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 20;
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'length' => 'integer',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'published_at',
    ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'name',
      'slug',
      'length',
    ];
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo | \App\Models\Show
     */
    public function show()
    {
        return $this->belongsTo(Show::class);
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany | \App\Models\User 
     */
    public function subscribers()
    {
        return $this->hasMany(User::class);
    }
}
```

### Table

Sometimes you may want to name your own table for the given Model, instead of letting Laravel to pluralize it automatically. In that case, just issue the `table` key with the name of the table to create for the model.

```yaml
Post:
  table: blog_posts
  columns:
    name: string 
```

The above will create a migration following the table name and reference it in the Model class:

```php
Schema::create('blog_posts', function (Blueprint $table) {
    $table->string('name');
});
```

```php
class Podcast extends Model
{
    protected $table = 'blog_posts';
}
```

### Columns

Columns names are defined by its their key name, while the type mimics the method calling in the Blueprint class. Additional arguments for the method are defined after the colon, and separated by comma:

```yaml
Post:
  columns:
    claps: integer:true,true index 
```

The above will create a migration like the following:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->integer('claps', true, true)->index();
});
```

> If you have a package that adds custom columns types, like `$table->custom('foo')`, no problem, columns types will be pushed as the method is.

#### Columns with no values

Columns keys with null values will transform into method names with no arguments. This is used for timestamps, soft-deletes and other short-hands from the [Blueprint](https://laravel.com/docs/7.x/migrations#columns).

```yaml
  timestampsTz: ~
  rememberToken: ~
```

The above will create:

```php
Schema::create('model_name', function (Blueprint $table) {
    $table->timestampsTz();
    $table->rememberToken();
});
```

#### Relations

To make relations, issue the name of the relation followed by a collection of the name of the column, and the type of the relation. You can pass arguments to it separated by comma.

```yaml
  Comment:
    columns: 
      id: ~
      author:
        relation: belongsTo:User

  User:
    columns:
      id: ~
      comments:
        relation: hasMany:Comment,user_id
```

> Columns are needed for relations like [`belongsTo`](https://laravel.com/docs/7.x/eloquent-relationships#one-to-one) and [`morphTo`](https://laravel.com/docs/7.x/eloquent-relationships#one-to-one-polymorphic-relations). If these aren't issued, Larakick will guess the column type based on the relation primary key if issued, and append the name of primary key as `_id`. If no primary key is set, it will be the default.

For example, the `author` relation of the `Comment` model will create a Model with the `author()` relation with a column name of `author_id`:

```php
Schema::create('users', function (Blueprint $table) {
    // ...
});

Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('author_id');
});

class User extends Model
{
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}

class Comment extends Model
{
    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
```

Alternatively, you can change the default column to create, and add a foreign constraint for some reason.

```yaml
Post:
  columns:
    id: ~
    author:
      column: author_id
      relation: belongsTo:User,id withDefault
      foreign: User onDelete:cascade
      # foreign: author_id references:id on:users onDelete:cascade
```

This will be reflected on the migration:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('author_id');
    $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
});
```

For Morph relations, you can just simply set it as `morphsTo`. If you don't add the column name to the `morphsTo` relation, it will be inferred in from the relation name.

```yaml
  Image:
    columns:
      id: ~
      imageable:
        relation: morphsTo

  Post:
    columns:
      id: ~
      cover:
        relation: morphOne:Image
```

This will create a migration and model like the following:

```php
Schema::create('images', function (Blueprint $table) {
    $table->id();
    $table->nullableMorphs('imageable');
});

class Image extends Model
{
    public function imageable()
    {
        return $this->morphTo(User::class);
    }
}

class Post extends Model
{
    public function cover()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
```

You can change the type of the polymorphic column using the `type` and using `morphs`, `nullableMorphs`,  `uuidMorphs`, `nullableUuidMorphs`. You can pass along the index name after a colon:

```yaml
columns:
  id: ~
  imageable:
    type: nullableUuidMorphs:imageable_type_id_index
    relation: morphsTo
```

### Soft Deletes

To make a model soft-deletable, just issue the `softDeletes` or `softDeletesTz` into the columns list. Larakick will automatically detect and use the `SoftDeletes` trait for the Model.

```yaml
Model:
  columns:
    # ...
    softDeletes: ~
```

The above will generate a Model like this:

```php
class Podcast extends Model
{
    use SoftDeletes;
}
```

Alternatively, you can issue the column name to use as soft-deletes, that will be reflected in the model itself.

```yaml
Model:
  columns:
    # ...
    softDeletes: soft_deleted_at
```

The above will generate a Model like this:

```php
class Podcast extends Model
{
    use SoftDeletes;
    
    protected const DELETED_AT = 'soft_deleted_at';
}
```

> Currently Larakick doesn't support non-timestamp soft delete columns, but you're free to create your own soft-deleted column logic after scaffolding.

### Primary Key

By default, if there is no `id` or incrementing column defined, it will be understood the model has no primary key, so it will be disabled for the model. It's always recommended to have a primary key, but you're covered if you have the rare case to no include one in your table.

To manually set a primary key column, or change it if there is already an `id` or incrementing column, you can by using the `primary` key. Larakick will guess the rest based on the column you select.

```yaml
Podcast:
  columns:
    id: ~
    slug: string
    # ...
  primary: slug
```

The above will create a migration like this:

```php
Schema::create('podcasts', function (Blueprint $table) {
    $table->string('slug');
    // ...

    $table->primary('slug');
});
```

Larakick, on the other hand, will guess the primary key type and incrementing nature in the Model:

```php
class Podcast extends Model
{
    protected $primary = 'slug';
    protected $keyType = 'string';
    protected $incrementing = false;
    
    // ...
}
```

> Eloquent ORM doesn't support Primary keys made from multiple columns (also called "Composite Primary"). For that reason, Composite Primary keys are not supported in Larakick. 

To avoid confusion, ensure you don't use the `primary` method in your column definition. Larakick will bypass that method name to ensure you set the primary key correctly.

For example, here, the `uuid` column won't be set as primary, but the `slug` will be. 

```yaml
Podcast:
  columns:
    id: ~
    uuid: uuid primary # The "primary" method name won't be used.
    slug: string
    # ...
  primary: slug
```

If you want your model to not have any primary key, ensure you set `primary` to false.

```yaml
Podcast:
  columns:
    id: ~
    uuid: uuid primary
    slug: string
    # ...
  primary: false
```

### Indexes

Indexes are not considered in the column definition, but rather in the `indexes` key. Here you can create an index for a single column, or multiple columns with a custom name for each.

Just issue the name of the column you want to index and you're done.

```yaml
Podcast:
  columns:
    id: ~
    show_id: unsignedBigInteger
    slug: string
    # ...
  indexes: slug
```

Alternatively, you can set one or many indexes with a custom name (specially if your SQL engine doesn't support large indexes) but using a key-value list:

```yaml
Podcast:
  columns:
    id: ~
    show_id: unsignedBigInteger 
    slug: string
    analytics_cache_identificator: unsignedBigInteger
    # ...
  indexes:
    show_id_analytics_index: show_id analytics_cache_identificator
```

If you issue just a list, Larakick will let Eloquent to come with a proper name for them.

```yaml
Podcast:
  columns:
    id: ~
    show_id: unsignedBigInteger 
    slug: string
    analytics_cache_identificator: unsignedBigInteger
    # ...
  indexes:
    - slug
    - show_id analytics_cache_identificator
```

> Don't issue `index` in the column definition. Larakick will bypass that statement.

### Fillable

You can add fillable properties to the Model right from the Model declaration using the `fillable` key. This key allows for mass assignation of properties to the Model itself.

```yaml
Model:
  columns:
    name: string
    email: string
    ...
  fillable:
    - name
    - email
```

The above will generate a Model like this:

```php
class Podcast extends Model
{
    protected $fillable = [
        'name',
        'email',
    ];
    
    // ...
}
```

> Currently Larakick doesn't merge the fillable attributes if you validate and save a model in your controllers. 

### Timestamps

By default, if you set a model with `timestamps` or `timestampsTz`, the model will have have a timestamp for `created_at` and `update_at`. If a model doesn't includes either of both, timestamps will be disabled.

You can change the default columns using the `timestamps` key. For example, you can disable the default timestamps and only add one to register the creation date. 

```yaml
Podcast:
  columns:
    # ...
    creation_date: timestamp
    
  timestamps:
    created_at: creation_date
```

The above will generate a Model like this:

```php
class Podcast extends Model
{
    protected const CREATED_AT = 'creation_date';
    protected const UPDATED_AT = null;
}
```


### Route Binding

You may want to change how to route bind the Model into the controllers later in your application. Instead of doing it manually in each controller, or setting it in your App Service Provider, you can simply override it using the `route` key and the name of the model property.

```yaml
models:
  Post:
    route: uuid
```

> It's always recommended to add `index` or `primary` to the column definition when you route-bind the Model to that column for performance reasons, if it's not set. For the above example, `index: uuid` should suffice. 

### Policies

Policies (CRUD authorization over a Model) are [handled separately in the Authorization Policies](AUTH.md#policies) for your sanity.

## Pivot Models

Some relations may need a Pivot table. Larakick will automatically create pivot tables for your `belongsToMany` and `morphToMany` relations by using Laravel naming convention. The Pivot table won't have a model, factory nor seeder.

```yaml
User:
  columns:
    id: ~
    name: string
    roles:
      relation: belongsToMany:Role as:permissionsSet

Role:
  columns:
    id: ~
    name: string
    users:
      relation: belongsToMany:User
```

The migrations and subsequent Models will be as follows:

```php
<?php

Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
});

Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name');
});

Schema::create('role_user', function (Blueprint $table) {
    $table->bigUnsignedInteger('user_id');
    $table->bigUnsignedInteger('role_id');
    $table->timestamps();
});

class User extends Model
{
    public function roles()
    {
        return $this->belongsToMany(Role::class)->as('permissionsSet');
    }
}

class Role extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
```

You can also create your own Pivot table, or a Morphable Pivot table, by setting the `type` key to pivot. If you set the `using:{PivotClass}` in your relation, Larakick will understand you're setting the pivot manually.

```yaml
User:
  columns:
    id: ~
    name: string
    subscriptions:
      relation: belongsToMany:Podcast using:Subscription withTimestamps withPivot:last_heard,updated_at,created_at

Podcast:
  columns:
    id: ~
    title: string
    subscribers:
      relation: belongsToMany:User using:Subscription

Subscription:
  columns:
    user:
      relation: belongsTo:User
    podcast:
      relation: belongsTo:Podcast
    last_heard: timestamp nullable
    timestamps: ~
  type: Pivot
```

The migrations and subsequent Models will be as follows:

```php
<?php

Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
});

Schema::create('podcasts', function (Blueprint $table) {
    $table->id();
    $table->string('name');
});

Schema::create('subscriptions', function (Blueprint $table) {
    $table->bigUnsignedInteger('user_id');
    $table->bigUnsignedInteger('podcast_id');
    $table->timestamp('last_heard')->nullable();
    $table->timestamps();
});

class User extends Model
{
    public function subscriptions()
    {
        return $this->belongsToMany(Podcast::class)->using(Subscription::class)
            ->withTimestamps()
            ->withPivot('last_heard', 'updated_at', 'created_at');
    }
}

class Podcast extends Model
{
    public function subscribers()
    {
        return $this->belongsToMany(User::class)->using(Subscription::class)->withTimestamps();
    }
}

class Subscription extends Pivot
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function podcast()
    {
        return $this->belongsTo(Podcast::class);
    }
}
```

> When creating [Pivot tables](https://laravel.com/docs/7.x/eloquent-relationships#defining-custom-intermediate-table-models), soft-deleted, primary keys and timestamps are automatically disabled. You can re-enable them using the [`primary`](#primary-key) and [`timestamps`](#timestamps) keys, but soft-deleted is bypassed since the framework still doesn't support it.

As you saw, you can use `withPivot` to name the columns you want to retrieve from the Pivot Model.

## Factories

Factories for Models are created automatically. 

```yaml
Podcast:
  columns:
    id: ~
    name: string
    published_at: timestamp nullable
    length: int
    timestamps:
```

```php
$factory->define(\App\Podcast::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'length' => $faker->length,
    ];
});
```

> Larakick will try to guess the `Faker` values for each property by using the name of the column, but in any case you should go to your factory and add the correct values yourself.

To disable creating factories, issue the `factory` key with the `false` value:

```yaml
Podcast:
  factory: false
```

## Seeders

Seeders are conveniently created for you. By default, a seeder for a model will be created automatically. To disable creating a seeder, just set the `seeder` as `false`.

```yaml
Podcast:
  # ...
  seeder: false
```

> While the database seeders will be created, you will have to manually add them to the `run()` method of your `database/seeds/DatabaseSeeder.php`.


## JSON Resources

You can push [JSON Resources](https://laravel.com/docs/7.x/eloquent-resources) for the model directly from YML file.

By default, JSON Resources are not created, but you can enable it using the `json` key. Once you set it to `true`, a JSON Resource will be created in `App\Http\Resources` and appended the `JsonResource` name.

```yaml
Podcast:
  columns:
    id: ~
    name: string
    length: int
    timestamps: ~
  json: true
```

When the `json` is `true`, it will automatically create the following:

```php
class PodcastJsonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'length' => $this->length,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```

The JSON Resource is created using all the Model properties in the returned array. You're free to edit the JSON Resource as you see fit.

> You can create [JSON Resource Collections](https://laravel.com/docs/5.8/eloquent-resources#resource-collections) manually after scaffolding.

## Repositories

Repositories are a way to intercede between the Models (which are a record abstraction for the database) and the controller themselves, by filtering appropriately the model operations.

You may want to use repositories if you need custom queries to manage a Model which may pollute the controllers itself and any other piece of code in your application.

```yaml
model:
   Post:
     repository: true
```

Following the above example, a Repository will be created in `App\Repositories\PostRepository` with sample code for CRUD operations: create, retrieve, update and delete.

You're free to implement an interface for the repositories or create an abstract class so these can extend it and remove duplicate code. This is an aid, not a manual.

> The repository implements the `UrlRoutable` contract that will allow you to use it in your controllers to route the given Model through the controller itself. You're free to implement them in your controllers manually.

## Global Scopes

Sometimes thinking ahead for Global Scopes is a good way to leverage creating and handling them in the model itself. Just issue the `scopes` key with a list to generate each automatically, ready to be edited by you.

```yaml
model:
  Post:
    scopes:
      - Unpublished
```

Each of the scopes are saved inside the `app/Scopes` directory. After that, you're free to add them to your application.

> Local scopes are not supported in Larakick... yet. But probably won't ever to avoid cluttering up the YAML file.

## Eloquent Observers

And finally, you can also create an [Observer](https://laravel.com/docs/5.8/eloquent#observers) for all Eloquent operations over the model, which may come handy.

> Observers are preferred instead of plain Eloquent Events.

Larakick will automatically create an observer based on the Model name and the Event fired, by just calling the appropriate artisan command.

```yaml
models:
  Post:
    observer: true
```

The above will create the observer in the `app\Observers\PostObserver` directory.

# Migrations

The `migrations` key represents a quick way to add migrations that are **not** tied to Models. For example, the `failed_jobs` tables:

```yaml
migrations:
  failed_jobs:
    id: ~
    connection: text
    queue: text
    payload: longText
    exception: longText
    failed_at: timestamp useCurrent
```

The migrations are defined using the table name as key, and a list of [columns](#columns). These are passed as-it-is to the migration class.

Following the above example, this will generate the following:

```php
Schema::create('failed_jobs', function (Blueprint $table) {
    $table->id();
    $table->text('connection');
    $table->text('queue');
    $table->longText('payload');
    $table->longText('exception');
    $table->timestamp('failed_at')->useCurrent();
});
```

> Factories and Seeders are not created for migrations. You must do that manually for each of them.

## Pivot Tables on Migrations

Larakick will guess pivot tables if you don't issue them, along with columns needed to reach each model of the `belongsToMany` and `morphsToMany` relations. So there is **no need to create migrations for pivot columns**.

While migrations with the same table name of Models will conflict, like a `Post` model and a `posts` table, migrations for pivot models can be overwritten safely.

In this example, we will set the `podcast_user` pivot table and manually add the columns we need. 

```yaml
models:
  User:
    columns:
      id: ~
      name: string
      subscriptions:
        relation: belongsToMany:Role as:Subscription withTimestamps withPivot:last_heard,updated_at,created_at
    
  Podcast:
    columns:
      id: ~
      title: string
      subscribers:
        relation: belongsToMany:User as:Subscription

migrations:

  podcast_user:
    podcast_id: unsignedBigInteger
    user_id: unsignedBigInteger index
    last_heard: timestamp nullable
    timestamps: ~
```

> When you overwrite a pivot table migration, ensure you set the relation columns correctly.
