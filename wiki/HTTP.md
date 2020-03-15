# HTTP

Larakick conveniently generates controllers for you, along with almost everything you need to make them work, including routes. You only have to point out your controllers, what you want them to do, and that's it.

Controllers in Larakick are defined in `larakick/http.yml`. Creating a controller will make the controller class along their tests. You don't have to do nothing.

```yaml
namespace: App\Http\Controllers

middleware:
  json:
    name: SaveBrowserFingerprint
  stats:
    name: SaveRequestStats
    terminable: true

controllers:
  PostController:
    middleware:
      - auth
      - json
    actions:
      index:
        queries:
          - posts: Post all
        response: view:post.index with:posts
      show:
        models:
          post: Post:id
        view: post.show with:post
      create:
        view: post.create
      store:
        validate:
          - title: required|string
          - body: required|string
        save: post:validated
        notify: PostPublishedNotification to:post.author with:post
        dispatch: RefreshHomepage with:post
        fire: PostCreated with:post.title
        flash: post.title with:post.title
        custom: "alert()->lang('post.created', ['post' => $post->title])->success()"
        redirect: post.show,post
      update:
        authorize: ~
        models: Post:id
        validate:
          - title: required|string
          - body: required|string
        save: post
        custom: 
          - "alert()->lang('post.updated', ['post' => $post->title])->success()"
        redirect: back
      delete:
        models: Post:id
        delete: post
        flash: post.title.deleted with:post.title
        redirect: post.index
```

> Controllers in Larakick are revolved around Models. If you need to create custom logic, you're better creating a custom controller yourself.

We are gonna go for each key so you can further customize what the HTTP Controller does, but first, let's start with the simplest controllers.

## Namespace

All namespaces for your project controllers are based on the `App\Http\Controllers`, which is the default in fresh Laravel installation.

To avoid too many files inside a directory, you can create controllers appending the corresponding namespace:

```yaml
namespace: App\Http\Controllers\Admin

controllers:
    
  User\PostController:
    # ...
  User\CommentController:
    # ...
``` 

For example, the above will create two controllers, following PSR-4 convention:

* `App\Http\Controllers\Admin\User\PostController`
* `App\Http\Controllers\Admin\User\CommentController`

## Middleware

Sometimes you want to create your own middleware classes. Instead of creating each one from scratch and adding the manually into your HTTP Kernel, you can just issue your middleware here. These will be appended to the middleware list of your application.

```yaml
middleware:
  json:
    name: SaveBrowserFingerprint
  stats:
    name: SaveRequestStats
    terminable: true
``` 

The above will create the `App\Http\Middleware\SaveBrowserFingerprint` and `App\Http\Middleware\SaveRequestStats`, with a [`terminate` method](https://laravel.com/docs/7.x/middleware#terminable-middleware). 

Your Kernel will be modified to add the middleware to the `$routeMiddleware` list:

```php
protected $routeMiddleware = [
    // ...
    'json' =>  \App\Http\Middleware\SaveBrowserFingerprint::class,
    'stats' =>  \App\Http\Middleware\SaveRequestStats::class,
];
```

> If the keys of the middleware are already used in the list by other middleware or package, you will receive an error. 

These can be referenced by your controller in the [`middleware` key](#controller-middleware).

## Actions

Actions are defined by their key. For example, if a controller has the action `show`, the same name will be used for its method.

```yaml
PostController:
  actions:
    showForm:
      # ...
```

```php
<?php

namespace App\Http\Controllers;

class PostController extends Controller
{
    public function showForm()
    {
        // ...
    }
}
```

#### List

Most of actions accepts a list of items using key-value or plain arrays:

```yaml
show:
  models:
    post: Post:uuid
    user: User
  dispatch:
    - RefreshHomepage with:post
    - NotifyEditors with:post
```

The list of things you can add to a controller definition are these:

| Name | Accepts list | Type |
| --- | --- | --- |
| `route` | ✖ | |
| `models` | ✔ | Key-value, values
| `authorize` | ✖ |  
| `validate` | ✔ | Key-value
| `queries` | ✔ | Key-value, values
| `save` | ✖ | 
| `delete` | ✔ | 
| `fire` | ✔ | Values
| `dispatch` | ✔ | Values
| `notify` | ✔ | Values
| `flash` | ✔ | Key-value, values
| `custom` | ✔ | Values
| `view` | ✖ | 
| `redirect` | ✖ | 

### Order

You can create your controller with actions in any order given, but Larakick will execute them in this order:

| Order | Name |
| --- | --- |
| 1 | `route` |
| 2 | `models` |
| 3 | `authorize` |
| 4 | `validate` |
| 5 | `queries` |
| 6 | `save` |
| 7 | `delete` |
| 8 | `fire` |
| 9 | `dispatch` |
| 10 | `notify` |
| 11 | `flash` |
| 12 | `custom` |
| 13 | `redirect` |
| 14 | `view` |

### Models

Specifies the model to use in the action as parameter. These will available as camel case (camelCase) inside the action.

```yaml
models: Post
```

```php
public function store(Post $post)
{
    // ...
}
```

Additionally, you can [issue the column from which the Model should be retrieved](https://laravel.com/docs/7.x/routing#route-model-binding).

```yaml
models: Post:slug
```

```php
Route::get('post/{post:slug}', 'PostController@show');
```

To change the name of the variable where the model is stored inside the action, define it as key-value:

```yaml
models:
  - publication: Post:slug
```

```php
public function store(Post $publication)
{
    // ...
}
```

And finally, you can also list many models into the `models` key using both ways, which is useful for nested resources.

```yaml
models: Post:slug User:id
```

or 

```yaml
models:
  - publication: Post:slug
  - author: User:id
```

> If you use custom variables, these will be automatically set in the Route name, like `posts/{publication:slug}`.


### Route

Each time you create a Controller action, Larakick will try to guess the route it should generate based on the name of the action.

```yaml
User\PostController:
  actions:
    show:
      models: Post
      # ...
    create:
      validate:
        - title: required|string
        - body: required|string
      # ...
```

```php
Route::get('user/post/{post}', 'User\PostController@show')
    ->name('user.post.show');
Route::post('user/post', 'User\PostController@create')
    ->name('user.post.create');
``` 

Alternatively, you can issue the route for the action yourself, and optionally, the name of it.

```yaml
User\PostController:
  actions:
    show:
      models: Post
      route: get:post-creator/post/{post:slug} post-creator.show
```

```php
Route::get('post-creator/post/{post:slug}', 'User\PostController@show')
    ->name('post-creator.show');
```

If you use a custom Model variable, you must define it as you have set it.

```yaml
User\PostController:
  actions:
    show:
      models: 
        - publication: Post
      route: get:post-creator/post/{publication:slug} post-creator.show
```

```php
Route::get('post-creator/post/{publication:slug}', 'User\PostController@show')
    ->name('post-creator.show');
```

### Authorize

To authorize a given action, you can issue the `authorize` key. Larakick will guess the action and model or class name automatically:

```yaml
update:
  models: Post:id
  authorize: ~
```

```php
public function update(Post $post)
{
    $this->authorize($post);
}
```

If you need more control, you can always pass the action and name of the model variable you want to authorize.

```yaml
update:
  models: Post:id
  authorize: update post
```

If you have changed the model variable, you must issue the same model variable to the authorization key:

```yaml
update:
  models:
    - publication: Post:id
  authorize: update publication
```

You can also pass the class name in case there is no record, like when happens with the `create` method. Larakick will always try to guess the Model name from the Controller name.

```yaml
create:
  authorize: create Post 
```

If you have set a [Model Policy](AUTHORIZATION.md#policies) matching the Model and Action names, [authorization will be automatically appended](AUTHORIZATION.md#automatic-authorization-in-controller-actions) even if you don't declare it explicitly, to save you time.

```yaml
# authorization.yml
policies:
  PostPolicy:
    actions:
      - update

# http.yml
update:
  models: Post:id
  redirect: back 
```

```php
public function update(Post $post)
{
    $this->authorize($post);

    return back();
}
```

To disable any authorization, you can set the `authorize` key to `false`. If the Model has a matching policy, this will not be present in the controller action code. 

```yaml
PostController:
  actions:
    update:
      models: Post:id
      authorize: false
```

> Beware of Form Requests with [`authorization`](AUTHORIZATION.md#form-authorization), ensure you don't authorize the same action two times with the same logic, specially when using [Gates](AUTHORIZATION.md#gates).

### Validate

Creates a Request with validation rules. Validated inputs are available as `$validated`

```yaml
PostController:
  actions:
    store:
      validate:
        - name: required|string
        - body: required|string
      # ...
```

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string',
        'body' => 'required|string',
    ]);
    // ...
}
```

#### Form Requests

If you want to create a [Form Request](https://laravel.com/docs/7.x/validation#form-request-validation) to centralize validation and authorization, set a form in the [Authorization YAML file](AUTHORIZATION.md#form-requests). 

Once the Form Request is set, you can further reference it in other controllers using the its name directly.

```yaml
AdminPostController:
  actions:
    store:
      validate: StorePostRequest
```

> Beware of Form Requests with [`authorization`](AUTHORIZATION.md#form-authorization), ensure you don't authorize the same action two times with the same logic, specially when using [Gates](AUTHORIZATION.md#gates).

### Queries

Creates retrieval queries and saves them into a variable specified by its key.

```yaml
queries:
  - posts: Post with:comments paginate
```

```php
$posts = Post::with('comments')->paginate();
```

### Save

Updates or Create a given Model using the Request validated properties

```yaml
save: post
```

```php
public function update(Request $request, Post $post)
{
    $validated = $request->validated([
        // ...
    ]);

    $post->fill($validated)->save();

    // ...
}
```

Larakick will automatically get the validated input from the Request or [Form Request](#form-requests).

Alternatively, you can merge (or replace) the values to save by issuing a key-value list. This is **mandatory** if you plan to save something into the model name and no validation was set previously. 

The values are passed as plain PHP.

```yaml
publish:
  models: 
    post: Post
  save: 
    post:
      - published_at: now()
      - slug: \Str::slug($post->title)
```

```php
public function publish(Post $post)
{
    $post->published_at = now();
    $post->slug = \Str::slug($post->title);
    
    $post->save();

    // ...
}
```

### Notify

Creates and dispatches a notification.

```yaml
  notify: PostPublishedNotification to:post.author with:post
```

```php
public function (StorePostRequest $request, Post $post)
{
    // ...

    Notification::send($post->author, new PostPublishedNotification($post));
}
```

The notification is automatically created in `App\Notifications`, along the parameters in the constructor.

### Dispatch

Creates and dispatches a given Job. By default, the Job created implements the `ShouldQueue` contract. 

```yaml
dispatch: RefreshHomepage with:post
```

```php
public function (StorePostRequest $request, Post $post)
{
    // ...

    RefreshHomepage::dispatch($post);
}
```

The Job is created in the `App\Jobs` directory, along with any parameter in its constructor.

### Fire

Creates and fires an Event.

```yaml
fire: PostCreated with:post.name
```

```php
public function (StorePostRequest $request, Post $post)
{
    // ...

    Event::dispatch(new PostCreated($post->name));
}
```

The Job is created in the `App\Events` directory, along with any parameter in its constructor.

### Flash

Flashes the given key and value into the session.

```yaml
flash: post.title with:post.name
```

```php
public function (StorePostRequest $request, Post $post)
{
    // ...

    Session::flash('post.title', $post->name);
}
```

### Custom

Executes the given list of raw PHP code. Useful when you're using third party packages.

The code must set inside double quotes or single quotes. It is pushed as-it-is to the controller. 

```yaml
custom: "alert()->lang('post.created', ['post' => $post->title])->success()"
```

```php
public function (StorePostRequest $request, Post $post)
{
    // ...

    alert()->lang('post.created', ['post' => $post->title])->success();
}
```

### View

Returns a view using the given parameters.

```yaml
view: post.show post
```

```php
public function (Post $post)
{
    // ...

    return view('post.show', ['post' => $post]);
}
```

You can change the name of the variable to add to the view using `view: post.show foo:bar`. The latter will return this:

```php
return view('post.show', ['foo' => $bar]);
```  

An empty Blade view will be automatically created. In this case, the view will be in `resources/views/post/show.blade.php`.

> I totally recommended to have all your web views in a folder like `resources/views/web/*` if you plan to have many of them.

### Redirect

Returns a redirect response back, to a given action or named route.

```yaml
redirect: route:post.show,post
```

```php
public function (Post $post)
{
    // ...

    return redirect()->route('post.show', $post);
}
```

You can redirect to a given action name using _Class@action_ notation.

```yaml
redirect: action:PostController@show,post post,post.title 
```

```php
public function (Post $post)
{
    // ...

    return redirect()->action('PostController@show', $post)->with('post', $post->title);
}
```

Finally, you can use `back` to redirect back. You can chain it with `with` to flash something into the session.

```yaml
redirect: back post,post.title
```

```php
public function (Post $post)
{
    // ...

    return back()->with('post', $post->title);
}
```

#### Flashing data into the session

When issuing redirect, you can automatically flash session data by just appending the key name and value:

```yaml
redirect: back foo,bar.quz
```

```php
public function (Post $post)
{
   return back()->with('foo', $bar->quz);
}
```

## Controller Middleware

Middleware can be set separately in-controller using the `middleware` key.

```yaml
PostController:
  middleware:
    - auth:web only:show,index
    - customMiddleware except:create
    - foo:bar,50
```

```php
<?php

namespace App\Http\Controller\PostController;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web')->only('show', 'index');
        $this->middleware('customMiddleware')->except('create');
        $this->middleware('foo:bar,50');
    }

    // ...
}
```

> The key doesn't care if the middleware exists or not.

## Resource controllers

A resource controller is the simplest form to create controllers related to a resource, or a nested resource.

```yaml
namespace: App\Http\Controllers

controllers:

  PostController:
    resource:
      models: Post
      only: index show

  UserPostController:
    resource:
      models: User Post
      only: index show
```

This will create a Resource Controller using the model set, and a default way to run the CRUD logic, saving many minutes creating each action.

Nested Resources Controllers are automatically detected if you issue more than one Model as a resource. 

You can override any method from the Resource controller with your own logic, like in this example where we customize the query to get all the posts. 

```yaml
UserPostController:
  resource:
    models: User Post
    only: index show

  actions:
    index:
      queries:
        - posts: User.posts.withTrashed.paginate
      view: user.post.index with:posts
```

## JSON Resources

If you want a controller to automatically generate [JSON Resource actions from a Model](MODELS.md#json-resources) , just issue the JSON Resource name as resource value.

```yaml
PodcastController:
  resource: PodcastJsonResource
```

Larakick will conveniently create a Controller with all CRUD operations based on the JSON Resource, and you will be able to create only or except certain actions.

```php
<?php

namespace App\Http\Controllers;

use App\Podcast;
use Illuminate\Http\Request;
use App\Http\Resources\PodcastJsonResource;

class PodcastController extends Controller
{
    public function index()
    {
        return new PodcastJsonResource(Podcast::paginate());
    }

    public function show(Podcast $podcast)
    {
        return new PodcastJsonResource($podcast);
    }

    public function update(Request $request, Podcast $podcast)
    {
        $validated = $request->validate([
            // ...
        ]);

        return new PodcastJsonResource($podcast->fill($validated)->tap()->save());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // ...
        ]);

        return new PodcastJsonResource(Podcast::create($validated));
    }

    public function delete(Podcast $podcast)
    {
        $podcast->delete();

        return new PodcastJsonResource($podcast);
    }
}
```

Since you may not want all the CRUD operations, you create CRUD methods for only certain actions, or create all except one.

```yaml
PodcastController:
  resource: PodcastJsonResource
  only: index delete

UserController:
  resource: UserJsonResource
  except: delete
```
