<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitLab.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\GitLab;

use Gitlab\Client;
use GrahamCampbell\GitLab\Auth\AuthenticatorFactory;
use GrahamCampbell\GitLab\Cache\ConnectionFactory;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

/**
 * This is the gitlab service provider class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class GitLabServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
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
    public function register()
    {
        $this->registerAuthFactory();
        $this->registerCacheFactory();
        $this->registerGitLabFactory();
        $this->registerManager();
        $this->registerBindings();
    }

    /**
     * Register the auth factory class.
     *
     * @return void
     */
    protected function registerAuthFactory()
    {
        $this->app->singleton('gitlab.authfactory', function () {
            return new AuthenticatorFactory();
        });

        $this->app->alias('gitlab.authfactory', AuthenticatorFactory::class);
    }

    /**
     * Register the cache factory class.
     *
     * @return void
     */
    protected function registerCacheFactory()
    {
        $this->app->singleton('gitlab.cachefactory', function (Container $app) {
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
    protected function registerGitlabFactory()
    {
        $this->app->singleton('gitlab.factory', function (Container $app) {
            $auth = $app['gitlab.authfactory'];
            $cache = $app['gitlab.cachefactory'];

            return new GitLabFactory($auth, $cache);
        });

        $this->app->alias('gitlab.factory', GitLabFactory::class);
    }

    /**
     * Register the manager class.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->singleton('gitlab', function (Container $app) {
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
    protected function registerBindings()
    {
        $this->app->bind('gitlab.connection', function (Container $app) {
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
    public function provides()
    {
        return [
            'gitlab.authfactory',
            'gitlab.factory',
            'gitlab',
            'gitlab.connection',
        ];
    }
}
