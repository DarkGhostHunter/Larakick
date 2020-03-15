# Models

Larakick makes setting your database a breeze, avoiding rounds of syncing your models with your migrations, factories, seeders, and most importantly, relations. 

Models in Larakick are defined in `larakick/models.yml`. Creating a model will spawn their migration, factory and seeder automatically. You don't have to do nothing.

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
    primary: id

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
        relation: hasMany:Comment
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

### Primary Key

By default, if there is no `id` or `incrementing` column defined, is understood the model has no primary key, so it will be disabled for the model. This happens automatically with [pivot models](#pivot-models). It's always recommended to have a primary key, but you're covered if you have the rare case to no include one in your table.

To set a primary key for other column, even if there is already an `id` or `incrementing`, you can override the primary key using the `primary` key. 

```yaml
Podcast:
  columns:
    id: ~
    slug: string
    ...
    
  primary:
    column: slug
    keyType: string
    incrementing: false
```

The above will create a migration like this:

```php
Schema::create('podcasts', function (Blueprint $table) {
    $table->string('slug');
    // ...

    $table->primary('slug');
});
```

Models, on the other hand, will define the primary key as follows:

```php
class Podcast extends Model
{
    protected $primary = 'slug';
    protected $keyType = 'string';
    protected $incrementing = false;
    
    // ...
}
```

> Eloquent ORM doesn't support Primary keys made from multiple columns (also called "Composite Primary").

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

### Timestamps

By default, all models have a timestamp for `created_at` and `update_at`. If a model doesn't includes either `timestamps` or `timestampTz` in the columns, timestamps will be disabled.

You can change the default columns using the `timestamps` key. For example, you can disable the default timestamps and only add one to register the creation date. 

```yaml
Podcast:
  columns:
    # ...
    creation_date: timestamp
    
  timestamps:
    created_at: creation_date
    updated_at: null
```

The above will generate a Model like this:

```php
class Podcast extends Model
{
    protected const CREATED_AT = 'creation_date';
    protected const UPDATED_AT = null;
}
```

### Policies

Policies (CRUD authorization over a Model) are [handled separately in the Authorization Policies](AUTHORIZATION.md#policies) for your sanity.

## Pivot Models

Some relations may need a Pivot table. To create a pivot table, or a morphable pivot table, you can set the `type` key to pivot.

```yaml
User:
  columns:
    id: ~
    name: string
    roles:
      relation: belongsToMany:Role using:RoleUser withPivot:created_at,updated_at

Role:
  columns:
    id: ~
    name: string
    users:
      relation: belongsToMany:User using:RoleUser

RoleUser:
  columns:
    user: ~
      column: user_id
      relation: belongsTo:User
    role:
      column: role_id
      relation: belongsTo:Role
    timestamps:
  type: Pivot
  # type: MorphPivot
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
        return $this->belongsToMany(Role::class)
            ->using(RoleUser::class)
            ->withPivot([
                'created_at',
                'updated_at'
            ]);
    }
}

class Role extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(RoleUser::class);
    }
}

class RoleUser extends Pivot
{
    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function roles()
    {
        return $this->belongsTo(Role::class);
    }
}
``` 

When creating [Pivot tables](https://laravel.com/docs/7.x/eloquent-relationships#defining-custom-intermediate-table-models), soft-deleted, primary keys and timestamps are automatically disabled. You can re-enable them using the [`primary`](#primary-key) and [`timestamps`](#timestamps) keys, but soft-deleted are bypassed.

## Factories

Factories for Models are created automatically. Larakick will try to guess the `Faker` values for each property, otherwise it will tell you what Factories needs "attention" so you can input the corresponding random values.

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
        'length' => '', // TODO: Assign a random value for the factory property.
    ];
});
```

To disable creating factories, issue the `factory` key with the `false` value:

```yaml
Podcast:
  factory: false
```

### States

Sometimes you may want to add states to the model factories for convenient and simple state management. Simply add the `states` key in the Model and put the attributes along the raw PHP code as value.

```yaml
Podcast:
  columns:
    # ...
  states:
    published:
      published_at: $faker->date(),
```

```php
$factory->state(\App\Podcast::class, 'published', function(Faker $faker) {
    return [
        'published_at' => $faker->date(),
    ];
});
```

## Seeders

Seeders are conveniently created for you. By default, a seeder for a model will be created for the default `perPage` key of the Model, which is 15.

If you want to change the number of records for the seeder, you can specify the `seed` key with the number of records to persist.

```yaml
Podcast:
  # ...
  perPage: 20
  seed: 10
```

```php
<?php

use Illuminate\Database\Seeder;
use App\Podcast;

class PodcastsTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(Podcast::class, 10)->create();
    }
}
```

> While the database seeders will be created, you will have to manually add them to the `run()` method of your `database/seeds/DatabaseSeeder.php`. This is because the application won't know the proper order of the seeders.

If you set `seed` to `false`, no seeder will be created.

```yaml
Podcast:
  # ...
  perPage: 20
  seed: false
```

## JSON Resources

You can push [JSON Resources](https://laravel.com/docs/7.x/eloquent-resources) from the model YML file directly. 

By default, these are not created, but you can enable them using the `json` key. Once you set it to `true`, a JSON Resource will be created in `App\Http\Resources` and appended the `JsonResource` name.


```yaml
Podcast:
  columns:
    id: ~
    name: string
    length: int
    timestamps: ~
  json: true
```

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

The JSON Resource is created using the Model properties in the returned array.

## Migrations

Migrations are not models, but just a quick way to add migrations that are **not** tied to a Model. For example, the `failed_jobs` tables.

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

## Repositories

Repositories are a way to intercede between the Models (which are a record abstraction for the database) and the controller themselves, by filtering appropriately the model operations.

You may want to use repositories if you need custom queries depending, for example, retrieving a model based on the user authenticated, while will limit the scope of them. It's just an idea.

```yaml
model:
   Post:
     repository: true
```

## Scopes

```yaml
model:
  Post:
    scopes:
      Unpublished:
        apply: where:publised_at,null
        enabled: false
```

```yaml
model:
  Post:
    scopes:
      Unpublished: ~
```

## Eloquent Events

```yaml
model:
  Post:
    events:
      created: PostCreated
      deleted: PostDeleted
```

`App\Events\Eloquent\Post\PostCreated`
