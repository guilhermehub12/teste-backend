<?php

declare(strict_types = 1);

namespace App\Providers;

use App\Contracts\Repository\Api\PostRepositoryInterface;
use App\Contracts\Repository\Api\UserRepositoryInterface;
use App\Repositories\Api\PostRepository;
use App\Repositories\Api\UserRepository;
use App\Services\Api\PostService;
use App\Services\Api\UserService;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Opcodes\LogViewer\Facades\LogViewer;
use Override;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserService::class, function ($app) {
            return new UserService($app->make(UserRepositoryInterface::class));
        });

        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(PostService::class, function ($app) {
            return new PostService($app->make(PostRepositoryInterface::class));
        });

        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->setupLogViewer();
        $this->configModels();
        $this->configCommands();
        $this->configUrls();
        $this->configDate();
        $this->configRateLimiting();

        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        Passport::enablePasswordGrant();
        Passport::hashClientSecrets();
    }

    /**
    * Sets up the LogViewer authentication to restrict access
    * based on whether the authenticated user is an admin.
    */
    private function setupLogViewer(): void
    {
        if (app()->environment('production')) {
            LogViewer::auth(fn ($request) => $request->user()?->is_admin);
        }
    }

    /**
     * Configures Eloquent models by disabling the requirement for defining
     * the fillable property and enforcing strict checking to ensure that
     * all accessed properties exist within the model.
     */
    private function configModels(): void
    {
        // --
        // Remove the need of the property fillable on each model
        Model::unguard();

        // --
        // Make sure that all properties being called exists in the model
        Model::shouldBeStrict();
    }

    /**
     * Configures database commands to prohibit execution of destructive statements
     * when the application is running in a production environment.
     */
    private function configCommands(): void
    {
        DB::prohibitDestructiveCommands(
            app()->isProduction()
        );
    }

    /**
     * Configures the application URLs to enforce HTTPS protocol for all routes.
     */
    private function configUrls(): void
    {
        if (app()->environment('production')) {
            URL::forceHttps();
        }
    }

    /**
     * Configures the application to use CarbonImmutable for date and time handling.
     */
    private function configDate(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configRateLimiting(): void
    {
        RateLimiter::for('register', function ($request) {
            return Limit::perMinute(1)->by($request->email);
        });
        RateLimiter::for('login', function ($request) {
            return Limit::perMinute(1)->by($request->email);
        });
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
