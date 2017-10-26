<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Repositories\CommentRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        $this->app->bind(PostRepository::class, function ($app, array $parameters) use ($request) {

            $user = $request->user();

            if (isset($parameters[0])) {
                $user = $parameters[0];
            }

            $model = $this->app->make(Post::class);

            if (isset($parameters[1])) {
                $model = $parameters[1];
            }

            return new PostRepository($model, $user);
        });

        $this->app->bind(CommentRepository::class, function ($app, array $parameters) use ($request) {

            $user = $request->user();

            if (isset($parameters[0])) {
                $user = $parameters[0];
            }

            $model = $this->app->make(Comment::class);

            if (isset($parameters[1])) {
                $model = $parameters[1];
            }

            return new CommentRepository($model, $user);
        });

        $this->app->bind(UserRepository::class, function ($app, array $parameters) use ($request) {

            $user = $request->user();

            if (isset($parameters[0])) {
                $user = $parameters[0];
            }

            $model = $this->app->make(User::class);

            if (isset($parameters[1])) {
                $model = $parameters[1];
            }

            return new UserRepository($model, $user);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {


    }

}
