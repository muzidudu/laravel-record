## Laravel Record

❤️ User record feature for Laravel Application.

[![CI](https://github.com/overtrue/laravel-record/workflows/CI/badge.svg)](https://github.com/overtrue/laravel-record/actions)
[![Latest Stable Version](https://poser.pugx.org/overtrue/laravel-record/v/stable.svg)](https://packagist.org/packages/overtrue/laravel-record)
[![Latest Unstable Version](https://poser.pugx.org/overtrue/laravel-record/v/unstable.svg)](https://packagist.org/packages/overtrue/laravel-record)
[![Total Downloads](https://poser.pugx.org/overtrue/laravel-record/downloads)](https://packagist.org/packages/overtrue/laravel-record)
[![License](https://poser.pugx.org/overtrue/laravel-record/license)](https://packagist.org/packages/overtrue/laravel-record)

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me-button-s.svg?raw=true)](https://github.com/sponsors/overtrue)

## Installing

```shell
composer require muzidudu/laravel-record -vvv
```

### Configuration & Migrations

```php
php artisan vendor:publish
```

## Usage

### Traits

#### `Muzidudu\LaravelRecord\Traits\Recorder`

```php

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Muzidudu\LaravelRecord\Traits\Recorder;

class User extends Authenticatable
{
    use Recorder;

    <...>
}
```

#### `Muzidudu\LaravelRecord\Traits\Recordable`

```php
use Illuminate\Database\Eloquent\Model;
use Muzidudu\LaravelRecord\Traits\Recordable;

class Post extends Model
{
    use Recordable;

    <...>
}
```

### API

```php
$user = User::find(1);
$post = Post::find(2);

$user->record($post);
$user->unrecord($post);
$user->toggleRecord($post);
$user->getRecordItems(Post::class)

$user->hasRecorded($post);
$post->hasBeenRecordedBy($user);
```

#### Get object recorders:

```php
foreach($post->recorders as $user) {
    // echo $user->name;
}
```

#### Get Record Model from User.

Used Recorder Trait Model can easy to get Recordable Models to do what you want.
_note: this method will return a `Illuminate\Database\Eloquent\Builder` _

```php
$user->getRecordItems(Post::class);

// Do more
$favortePosts = $user->getRecordItems(Post::class)->get();
$favortePosts = $user->getRecordItems(Post::class)->paginate();
$favortePosts = $user->getRecordItems(Post::class)->where('title', 'Laravel-Record')->get();
```

### Aggregations

```php
// all
$user->records()->count();

// with type
$user->records()->withType(Post::class)->count();

// recorders count
$post->recorders()->count();
```

List with `*_count` attribute:

```php
$users = User::withCount('records')->get();

foreach($users as $user) {
    echo $user->records_count;
}


// for Recordable models:
$posts = Post::withCount('recorders')->get();

foreach($posts as $post) {
    echo $post->records_count;
}
```

### Attach user record status to recordable collection

You can use `Recorder::attachRecordStatus($recordables)` to attach the user record status, it will set `has_recorded` attribute to each model of `$recordables`:

#### For model

```php
$post = Post::find(1);

$post = $user->attachRecordStatus($post);

// result
[
    "id" => 1
    "title" => "Add socialite login support."
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_recorded" => true
 ],
```

#### For `Collection | Paginator | LengthAwarePaginator | array`:

```php
$posts = Post::oldest('id')->get();

$posts = $user->attachRecordStatus($posts);

$posts = $posts->toArray();

// result
[
  [
    "id" => 1
    "title" => "Post title1"
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_recorded" => true
  ],
  [
    "id" => 2
    "title" => "Post title2"
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_recorded" => false
  ],
  [
    "id" => 3
    "title" => "Post title3"
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_recorded" => true
  ],
]
```

#### For pagination

```php
$posts = Post::paginate(20);

$user->attachRecordStatus($posts);
```

### N+1 issue

To avoid the N+1 issue, you can use eager loading to reduce this operation to just 2 queries. When querying, you may specify which relationships should be eager loaded using the `with` method:

```php
// Recorder
$users = User::with('records')->get();

foreach($users as $user) {
    $user->hasRecorded($post);
}

// Recordable
$posts = Post::with('records')->get();
// or
$posts = Post::with('recorders')->get();

foreach($posts as $post) {
    $post->isRecordedBy($user);
}
```

### Events

| **Event**                                     | **Description**                             |
| --------------------------------------------- | ------------------------------------------- |
| `Muzidudu\LaravelRecord\Events\Recorded`   | Triggered when the relationship is created. |
| `Muzidudu\LaravelRecord\Events\Unrecorded` | Triggered when the relationship is deleted. |

## Related packages

-   Follow: [overtrue/laravel-follow](https://github.com/overtrue/laravel-follow)
-   Like: [overtrue/laravel-like](https://github.com/overtrue/laravel-like)
-   Record: [overtrue/laravel-record](https://github.com/overtrue/laravel-record)
-   Subscribe: [overtrue/laravel-subscribe](https://github.com/overtrue/laravel-subscribe)
-   Vote: [overtrue/laravel-vote](https://github.com/overtrue/laravel-vote)
-   Bookmark: overtrue/laravel-bookmark (working in progress)

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/overtrue/laravel-records/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/overtrue/laravel-records/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## :heart: Sponsor me

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me.svg?raw=true)](https://github.com/sponsors/overtrue)

如果你喜欢我的项目并想支持它，[点击这里 :heart:](https://github.com/sponsors/overtrue)

## Project supported by JetBrains

Many thanks to Jetbrains for kindly providing a license for me to work on this and other open-source projects.

[![](https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg)](https://www.jetbrains.com/?from=https://github.com/overtrue)

## PHP 扩展包开发

> 想知道如何从零开始构建 PHP 扩展包？
>
> 请关注我的实战课程，我会在此课程中分享一些扩展开发经验 —— [《PHP 扩展包实战教程 - 从入门到发布》](https://learnku.com/courses/creating-package)

## License

MIT
