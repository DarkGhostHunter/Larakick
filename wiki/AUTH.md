# Authorization

Authorization may be a pain in the ass to manage, but Larakick makes it a breeze, since it follows the same Gate and Policies logic from Laravel in a convenient YAML file where you can see all authorization logic in a single glance.

Authorization is optional, but you can add your own in the `larakick/auth.yml` file.

```yaml
gates:
  AdminGate:
    - change-email
    - change-username
  UserGate:
    - publish-posts
    - pay-bill
    - close-account
    - reactivate-account
  AnalyticsGate:
    - show: showAnalytics

policies:
  CommentPolicy: ~
  PostPolicy:
    model: Post
  PodcastPolicy:
    actions:
      - store
      - update

forms:
  StorePostRequest:
    validate:
      - name: required|string
      - body: required|string
    authorization:
      - UserGate: publish-posts
```

## Gates

Gates revolve around a custom action and custom logic. Just define the class name, the action names and you will be ready.

```yaml
gates:
  AdminGate:
    - change-email
    - change-username
  UserGate:
    - pay-bill
    - close-account
    - reactivate-account
  AnalyticsGate:
    - show: showAnalytics
```

The above will create three Gates in the `App\Gates` directory, using _camelCase_ to declare each action name.

```php
<?php

namespace App\Gates;

class AnalyticsGate
{
    public function changeEmail($user)
    {
        return true;
    }

    public function changeUsername($user)
    {
        return true;
    }
}
```

As you read, you can customize the name of the gate and the method it references:

```yaml
AnalyticsGate:
  - show: showAnalytics
```
```php
<?php

namespace App\Gates;

class AdminGate
{
    public function showAnalytics($user)
    {
        return true;
    }
}
```

Gates are automatically registered into your `App\AuthServiceProvider` by adding a [`RegisterGates` trait](../src/Generation/Gates/RegistersGates.php), and creating a custom method, where each Gate is transformed into `gate:action-name` notation automatically to avoid duplicated keys.

```php
public function gates()
{
    return [
        'admin:change-email'        => 'App\Gates\AdminGate@changeEmail',
        'admin:change-username'     => 'App\Gates\AdminGate@changeUsername',

        'user:pay-bill'             => 'App\Gates\UserGate@payBill',
        'user:publish-posts'        => 'App\Gates\UserGate@publishPosts',
        'user:close-account'        => 'App\Gates\UserGate@closeAccount',
        'user:reactivate-account'   => 'App\Gates\UserGate@reactivateAccount',
        
        'analytics:show'            => 'App\Gates\AnalyticsGate@showAnalytics'
    ];
}
```

## Policies

To have proper authorization to manipulate a model, you can register [Policies](https://laravel.com/docs/7.x/authorization#creating-policies). To automate the policy creation task, just issue the name of the Policy. 

```yaml
policies:
  CommentPolicy: ~
```

The above will create al CRUD policies for the `Comment` model. The policy will be created in `App\Policies\CommentPolicy`.

Policies are created a directory deep where the Models base namespace are. If the base namespace is `App\Models`, then policies will be created in `App\Models\Policies`.

Larakick will guess the Model name based on the Policy name, so `PodcastPolicy` will affect the `Podcast` model. You can set the proper Model name using the `model` key.

```yaml
policies:
  PostPolicy:
    model: Post
```

If you want to specify custom policies for the Model, you can issue a custom list that will create each specified method.

```yaml
policies:
  PodcastPolicy:
    actions:
      - viewAny
      - update
      - publish
      - unpublish
```

> If you use `index` as an action name, it will be automatically mapped to `viewAny`.

### Automatic authorization in Controller Actions

Models that have a Policy will be [automatically referenced in the controllers action](HTTP.md#authorize) that use the Model and have the **same method name** registered in the policy.

```yaml
# http.yml
PostController:
    actions:
      create:
        view: post.create
      publish:
        models: Post
        save:
          post:
            - published_at: "now()" 
        view: post.show post

# auth.yml
policies:
  PostPolicy:
    actions:
      - create
      - publish
```

Following the example above, notice we didn't add the `authorize` key in the controller. We have a `PostController`, with a `PostPolicy` that contains the `create` and `publish` actions. Larakick will automatically put the Policy authorization layer on both actions.

```php
<?php

class PostController extends Controller
{
    public function create()
    {
        $this->authorize('create');

        return view('post.create');
    }

    public function publish(Post $post)
    {
        $this->authorize('publish', $post);

        $post->published_at = now();
        $post->save();

        return view('post.show')->with('post', $post);
    }
}
```

You can [disable this by setting `authorize` to `false`](HTTP.md#authorize).

## Form Requests

Forms Requests conveniently centralizes validation and authorization in one place. You can create them here to avoid polluting the HTTP or Model YAML files.

Just issue the name of the Form Request and validation rules you want.

```yaml
forms:
  StorePostRequest:
    validate:
      - name: required|string
      - body: required|string
```

The Request will be created with the validation rules set:

```php
class StorePostRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string',
            'body'  => 'required|string',
        ];
    }
}
```

In your controllers, just reference it [under the `validate` key](HTTP.md#form-requests):

```yaml
# http.yml
AdminPostController:
  actions:
    store:
      validate: StorePostRequest
```

### Form Authorization

If you also want to authorize the request with a Gate, you can add the `authorization` key to the form.

To make authorization coding faster, you can simply issue the Gate name using _Gate:action-name_ notation.

```yaml
forms:
  StorePostRequest:
    validate:
      - name: required|string
      - body: required|string
    authorize: UserGate:publish-posts
  StoreCommentRequest:
    validate:
      - body: required|string
    authorize: GuestGate:comment
```

> Beware of Form Requests with [`authorization`](AUTH.md#form-authorization), ensure you don't authorize the same action two times with the same logic, specially when using [Gates](AUTH.md#gates).

The above will create the following Form Request:

```php
class StorePostRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('user:publish-posts');
    }   

    public function rules()
    {
        return [
            'title' => 'required|string',
            'body'  => 'required|string',
        ];
    }
}
```

> If you need to authorize an action over a Model, it's recommended to [use Policies](#policies). Trying to do run a Policy authorization in the `authorize()` will need to recover the model manually from the database.
> When using Route Model Binding, which is **resolved outside the Form Request**, it means you may query the same record two times without noticing.
