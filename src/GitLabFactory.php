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
use Http\Client\Common\Plugin\RetryPlugin;
use Illuminate\Contracts\Cache\Factory;
use InvalidArgumentException;
use Symfony\Component\Cache\Adapter\SimpleCacheAdapter;

/**
 * This is the gitlab factory class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class GitLabFactory
{
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

        if ($url = array_get($config, 'url')) {
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

        if ($backoff = array_get($config, 'backoff')) {
            $builder->addPlugin(new RetryPlugin(['retries' => $backoff === true ? 2 : $backoff]));
        }

        if ($cache = array_get($config, 'cache')) {
            $builder->addCache(new SimpleCacheAdapter($this->cache->store($cache === true ? null : $cache)));
        }

        return $builder;
    }
}
