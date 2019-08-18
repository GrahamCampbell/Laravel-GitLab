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
use GrahamCampbell\GitLab\Authenticators\AuthenticatorFactory;
use GrahamCampbell\GitLab\Http\ClientBuilder;
use GrahamCampbell\GitLab\Http\Psr16Cache;
use Http\Client\Common\Plugin\RetryPlugin;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Symfony\Component\Cache\Adapter\Psr16Adapter;
use Symfony\Component\Cache\Adapter\SimpleCacheAdapter;

/**
 * This is the gitlab factory class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class GitLabFactory
{
    /**
     * The minimum cache lifetime of 12 hours.
     *
     * @var int
     */
    const MIN_CACHE_LIFETIME = 43200;

    /**
     * The maximum cache lifetime of 48 hours.
     *
     * @var int
     */
    const MAX_CACHE_LIFETIME = 172800;

    /**
     * The authenticator factory instance.
     *
     * @var \GrahamCampbell\GitLab\Authenticators\AuthenticatorFactory
     */
    protected $auth;

    /**
     * The illuminate cache factory instance.
     *
     * @var \Illuminate\Contracts\Cache\Factory
     */
    protected $cache;

    /**
     * Create a new gitlab factory instance.
     *
     * @param \GrahamCampbell\GitLab\Authenticators\AuthenticatorFactory $auth
     * @param \Illuminate\Contracts\Cache\Factory                        $cache
     *
     * @return void
     */
    public function __construct(AuthenticatorFactory $auth, Factory $cache)
    {
        $this->auth = $auth;
        $this->cache = $cache;
    }

    /**
     * Make a new gitlab client.
     *
     * @param string[] $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \Gitlab\Client
     */
    public function make(array $config)
    {
        $client = new Client($this->getBuilder($config));

        if (!array_key_exists('method', $config)) {
            throw new InvalidArgumentException('The gitlab factory requires an auth method.');
        }

        if ($url = Arr::get($config, 'url')) {
            $client->setUrl($url);
        }

        if ($config['method'] === 'none') {
            return $client;
        }

        return $this->auth->make($config['method'])->with($client)->authenticate($config);
    }

    /**
     * Get the http client builder.
     *
     * @param string[] $config
     *
     * @return \GrahamCampbell\GitLab\Http\ClientBuilder
     */
    protected function getBuilder(array $config)
    {
        $builder = new ClientBuilder();

        if ($backoff = Arr::get($config, 'backoff')) {
            $builder->addPlugin(new RetryPlugin(['retries' => $backoff === true ? 2 : $backoff]));
        }

        if ($cache = Arr::get($config, 'cache')) {
            $builder->addCache($this->getCacheAdapter($cache));
        }

        return $builder;
    }

    /**
     * Get the symfony cache adapter for the given illuminate store.
     *
     * @param bool|string $name
     *
     * @return \Symfony\Component\Cache\Adapter\AdapterInterface
     */
    protected function getCacheAdapter($name)
    {
        $store = $this->cache->store($name === true ? null : $name);

        $repo = new Psr16Cache($store, self::MIN_CACHE_LIFETIME, self::MAX_CACHE_LIFETIME);

        return class_exists(Psr16Adapter::class) ? new Psr16Adapter($repo) : new SimpleCacheAdapter($repo);
    }
}
