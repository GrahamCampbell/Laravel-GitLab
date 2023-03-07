<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitLab.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\GitLab;

use Gitlab\Client;
use GrahamCampbell\GitLab\Auth\AuthenticatorFactory;
use GrahamCampbell\GitLab\Cache\ConnectionFactory;
use GrahamCampbell\GitLab\HttpClient\BuilderFactory;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory as GuzzlePsrFactory;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

/**
 * This is the gitlab service provider class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class GitLabServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    private function setupConfig(): void
    {
        $source = realpath($raw = __DIR__.'/../config/gitlab.php') ?: $raw;

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('gitlab.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('gitlab');
        }

        $this->mergeConfigFrom($source, 'gitlab');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerHttpClientFactory();
        $this->registerAuthFactory();
        $this->registerCacheFactory();
        $this->registerGitLabFactory();
        $this->registerManager();
        $this->registerBindings();
    }

    /**
     * Register the http client factory class.
     *
     * @return void
     */
    private function registerHttpClientFactory(): void
    {
        $this->app->singleton('gitlab.httpclientfactory', function (): BuilderFactory {
            $psrFactory = new GuzzlePsrFactory();

            return new BuilderFactory(
                new GuzzleClient(['connect_timeout' => 10, 'timeout' => 30]),
                $psrFactory,
                $psrFactory,
                $psrFactory,
            );
        });

        $this->app->alias('gitlab.httpclientfactory', BuilderFactory::class);
    }

    /**
     * Register the auth factory class.
     *
     * @return void
     */
    private function registerAuthFactory(): void
    {
        $this->app->singleton('gitlab.authfactory', function (): AuthenticatorFactory {
            return new AuthenticatorFactory();
        });

        $this->app->alias('gitlab.authfactory', AuthenticatorFactory::class);
    }

    /**
     * Register the cache factory class.
     *
     * @return void
     */
    private function registerCacheFactory(): void
    {
        $this->app->singleton('gitlab.cachefactory', function (Container $app): ConnectionFactory {
            $cache = $app->bound('cache') ? $app->make('cache') : null;

            return new ConnectionFactory($cache);
        });

        $this->app->alias('gitlab.cachefactory', ConnectionFactory::class);
    }

    /**
     * Register the gitlab factory class.
     *
     * @return void
     */
    private function registerGitLabFactory(): void
    {
        $this->app->singleton('gitlab.factory', function (Container $app): GitLabFactory {
            $builder = $app['gitlab.httpclientfactory'];
            $auth = $app['gitlab.authfactory'];
            $cache = $app['gitlab.cachefactory'];

            return new GitLabFactory($builder, $auth, $cache);
        });

        $this->app->alias('gitlab.factory', GitLabFactory::class);
    }

    /**
     * Register the manager class.
     *
     * @return void
     */
    private function registerManager(): void
    {
        $this->app->singleton('gitlab', function (Container $app): GitLabManager {
            $config = $app['config'];
            $factory = $app['gitlab.factory'];

            return new GitLabManager($config, $factory);
        });

        $this->app->alias('gitlab', GitLabManager::class);
    }

    /**
     * Register the bindings.
     *
     * @return void
     */
    private function registerBindings(): void
    {
        $this->app->bind('gitlab.connection', function (Container $app): Client {
            $manager = $app['gitlab'];

            return $manager->connection();
        });

        $this->app->alias('gitlab.connection', Client::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides(): array
    {
        return [
            'gitlab.httpclientfactory',
            'gitlab.authfactory',
            'gitlab.cachefactory',
            'gitlab.factory',
            'gitlab',
            'gitlab.connection',
        ];
    }
}
