# Models

Models in Larakick are defined in `kickoff/models.yml`. Creating a model will spawn their migration, factory and seeder automatically.

The schema is very basic:

```yaml
namespace: App\

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
```

## Namespace

The namespace of all the Models are created as the `namespace` key says. Every model will prepend the namespace as says.

You can change this to your convenience. The path of the models will mirror the namespace as per PSR-4.

```yaml
namespace: Any\Namespace\You\Want
```

## Model

Models are defined by its key, and reflects the Model name as it is. Migrations are created using using studly case on plural.

For example, `GameLeaderboard` will make a table called `game_leaderboards`.

### Automated logic

When creating a model, the following logic will be automated for you:

* Casting columns to their appropriate type (strings, integers, floats, ...).
* Declaring columns as `date` and `datetime`.
* PHPDoc mixin for Eloquent Builder, `create` and `make` methods.
* PHPDoc blocks for model properties and relations.

For example, this `Podcast` model will create the following model:

```yaml
namespace: App\Models

models:
  Podcast:
    columns: 
      uuid: ~
      show: ~
        column: show_uuid
        relation: belongsTo:Show withDefault
      name: string
      slug: string
      length: int
      published_at: timestamp
      timestamps: ~
      softDeletes: ~
    perPage: 20
    primary: ~
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
        'created_at',
        'updated_at',
        'deleted_at',
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
}
```

### Columns

Columns names are defined by its key, while the type mimics the method calling in the Blueprint class. Arguments for the method are defined after the colon, and separated by comma:

```yaml
  ModelName:
     columns:
       name: type:foo,bar modifier:quz thing
```

The above will create a migration like the following:

```php
Schema::table('model_name', function (Blueprint $table) {
    $table->type('name', 'foo', 'bar')->modifier('quz')->thing();
});
```

> If you have a package that adds custom columns types, like `$table->custom('foo')`, no problem, columns types will be pushed at their are.

#### Columns with no values

If a column key has no value, a method with no arguments will be pushed.

```yaml
  timestampsTz: ~
  rememberToken: ~
```

The above will create:

```php
Schema::table('model_name', function (Blueprint $table) {
    $table->timestampsTz();
    $table->rememberToken();
});
```

### Soft Deletes

To make a model soft-deletable, just issue the `softDeletes` or `softDeletesTz` into the columns list. Larakick will automatically detect and use the `SoftDeletes` trait.

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

If you don't issue the `column` key, Larakick will guess the column type based on the relation primary key if issued, and append the name of primary key as `_id`. If no primary key is set, it will be the default.

For example, the `author` relation of the `Comment` model will create a Model with the `author()` relation with a column name of `author_id`:

```php
Schema::table('comments', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('author_id');
});

class Comment extends Model
{
    public function author()
    {
        return $this->belongsTo(User::class);
    }
}

class User extends Model
{
    public function comments()
    {
        return $this->hasMany(Comment::class);
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
```

This will be reflected on the migration:

```php
Schema::table('posts', function (Blueprint $table) {
    // ...
    $table->foreign('author_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');
});
```

For Morph relations, you can just simply set it as `morphsTo`.  

```yaml
  Image:
    columns:
      id: ~
      imageable:
        nullable: false
        relation: morphsTo:imageable

  Post:
    columns:
      id: ~
      image:
        relation: morphOne:Image,imageable
```

This will create a migration and model like the following:

```php
Schema::table('images', function (Blueprint $table) {
    $table->id();
    $table->morphs('imageable');
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
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
```

### Primary Key

By default, if there is no `id` or `incrementing` column defined, is understood the model has no primary key, so these will be disabled for the model.

To set a primary key from other column, you can override the primary key using the `primary` key. 

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
Schema::table('podcasts', function (Blueprint $table) {
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
    protected $incrementing = false
    
    // ...
}
```

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
      'name', 'email',
    ];
    
    // ...
}
```

### Timestamps

By default, all models have a timestamp for `created_at` and `update_at`. If a model doesn't includes either `timestamps` or `timestampTz`, timestamps will be disabled.

You can change the default columns using the `timestamps` key.

```yaml
Podcast:
  columns:
    ...
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

Policies are Gates logic revolving CRUD operations over a Model. For your sanity, [authorization is handled separately](AUTHORIZATION.md).

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

Schema::table('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
});

Schema::table('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name');
});

Schema::table('role_user', function (Blueprint $table) {
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

### States

Sometimes you may want to add states to the model factories for convenient and simple state management.


```yaml
Podcast:
  columns:
    # ...
  states:
    published:
      published_at: now(),
```

```php
$factory->state(\App\Podcast::class, 'published', function(Faker $faker) {
    return [
        'published_at' => now()
    ];
});
```

## Seeders

Seeders are conveniently created for you. By default, a seeder for a model will be created for the default `perPage` key of the Model, which is 15, but can be overridden.

If you want to change the number of records for the seeder, you can specify the `seed` key with the number of records to persist.

```yaml
namespace: App\Models

models:
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

## JSON Resources

You can push [JSON Resources](https://laravel.com/docs/7.x/eloquent-resources) from the model YML file directly. 

By default, these are not created, but you can enable them using the `jsonResource` key. Once you set it to `true`, a JSON Resource will be created in `App\Http\Resources`.


```yaml
Podcast:
  columns:
    id: ~
    name: string
    length: int
    timestamps: ~
  resource: true
```

```php
class Podcast extends JsonResource
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
