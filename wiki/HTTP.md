# HTTP

Larakick conveniently generates controllers for you, along with almost everything you need to make them work, including routes. You only have to point out your controllers and that's it.

```yaml
namespace: App\Http\Controllers

controllers:

  PostController:
    middleware:
      - auth
    actions:
      index:
        queries:
          posts: Post.all
        response: view:post.index with:posts
      update:
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
        custom: 
          - alert()->lang('post.created', ['post' => $post->title])->success()
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

### Route

Each time you create a Controller action, Larakick will try to guess the route it should generate.

```yaml
User\PostController:
  actions:
    show:
      model: Post
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
      model: Post
      route: get:post-creator/post/{post:slug} post-creator.show
```

```php
Route::get('post-creator/post/{post:slug}', 'User\PostController@show')
    ->name('post-creator.show');
```

### Model

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

Additionally, you can issue the column from which the Model should be retrieved.

```yaml
models: Post:slug
```

```php
Route::get('post/{post:slug}', 'PostController@show');
```

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

This will automatically create a `StorePostRequest`, which is the action name (`Store`), the name given created by the Controller name (minus the `Controller` word), and `Request`.

```php
public function store(StorePostRequest $request)
{
    // ...
}
```

The Request will be created with the validation rules set:

```php
<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string',
            'body'  => 'required|string',
        ];
    }
}
```

### Queries

Creates retrieval queries and saves them into a variable specified by its key.

```yaml
queries:
  posts: Post with:comments paginate
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
fire: PostCreated with:post
```

```php
public function (StorePostRequest $request, Post $post)
{
    // ...

    Event::dispatch(new PostCreated($post));
}
```

### Flash

Flashes the given key and value into the session.

```yaml
flash: post.title with:post
```

```php
public function (StorePostRequest $request, Post $post)
{
    // ...

    Session::flash('post.title', $post);
}
```

### Custom

Executes the given list of raw PHP code.

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

### Render

Returns a view using the given parameters.

```yaml
render: post.show with:post
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
      model: Post
      only: index,show
    actions:
      index:
        validate:
          - title: required|string
          - body: required|string
        save: post
        redirect: back
      show:
        render: post.show with:post
```

This will create a Resource Controller using the model set.

To create nested resources, you can set the nested resource in the model.

```yaml
resource:
  model: Post Controller
  only: index,show
```
