# HTTP

Larakick conveniently generates controllers for you, along with almost everything you need to make them work, including routes. You only have to point out your controllers, what you want them to do, and that's it.

```yaml
namespace: App\Http\Controllers

controllers:

  PostController:
    middleware:
      - auth
    actions:
      index:
        queries:
          - posts: Post.all
        response: view:post.index with:posts
      show:
        models:
          post: Post:id
          view: post.show with:post
      update:
        authorize: ~
        models: Post:id
        validate:
          - title: required|string
          - body: required|string
        save: post
        custom: 
          - alert()->lang('post.updated', ['post' => $post->title])->success()
        redirect: back
      store:
        validate:
          - title: required|string
          - body: required|string
        save: post:validated
        notify: PostPublishedNotification to:post.author with:post
        dispatch: RefreshHomepage with:post
        fire: PostCreated with:post
        flash: post.title with:post
        custom: alert()->lang('post.created', ['post' => $post->title])->success()
        redirect: post.show,post
```

> Controllers in Larakick are revolved around Models. If you need to create custom logic, you're better creating a custom controller yourself.

We are gonna go for each key so you can further customize what the HTTP Controller does, but first, let's start with the simplest controllers.

## Namespaces

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

The following keys accept a single value or a list:

* [`queries`](#Queries)
* [`models`](#Models)
* [`notify`](#Notify)
* [`dispatch`](#Dispatch)
* [`fire`](#Fire)
* [`flash`](#Flash)
* [`custom`](#Custom)

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
public function (StorePostRequest $request, Post $post)
{
    $post->fill($request->validated())->save();

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

```yaml
custom: alert()->lang('post.created', ['post' => $post->title])->success()
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
view: post.show with:post
```

```php
public function (Post $post)
{
    // ...

    return view('post.show', ['post' => $post]);
}
```

### Redirect

Returns a redirect response back, to a given action or named route.

```yaml
redirect: route:post.show with:post
```

```php
public function (Post $post)
{
    // ...

    return redirect()->route('post.show')->with('post', $post);
}
```

You can redirect to a given action name using _Class@action_ notation.

```yaml
redirect: action:PostController@show with:post
```

```php
public function (Post $post)
{
    // ...

    return redirect()->action('PostController@show')->with('post', $post);
}
```

Finally, you can use `back` to redirect back.

```yaml
redirect: back with:post
```

```php
public function (Post $post)
{
    // ...

    return back()->with('post', $post);
}
```

### Middleware

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
