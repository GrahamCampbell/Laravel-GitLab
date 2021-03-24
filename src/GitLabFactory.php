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
use Gitlab\HttpClient\Builder;
use GrahamCampbell\GitLab\Auth\AuthenticatorFactory;
use GrahamCampbell\GitLab\Cache\ConnectionFactory;
use Http\Client\Common\Plugin\LoggerPlugin;
use Http\Client\Common\Plugin\RetryPlugin;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\Psr16Adapter;

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
     * @var \GrahamCampbell\GitLab\Auth\AuthenticatorFactory
     */
    protected $auth;

    /**
     * The cache factory instance.
     *
     * @var \GrahamCampbell\GitLab\Cache\ConnectionFactory
     */
    protected $cache;

    /**
     * The logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Create a new gitlab factory instance.
     *
     * @param \GrahamCampbell\GitLab\Auth\AuthenticatorFactory $auth
     * @param \GrahamCampbell\GitLab\Cache\ConnectionFactory   $cache
     * @param \Psr\Log\LoggerInterface                         $logger
     *
     * @return void
     */
    public function __construct(AuthenticatorFactory $auth, ConnectionFactory $cache, LoggerInterface $logger)
    {
        $this->auth = $auth;
        $this->cache = $cache;
        $this->logger = $logger;
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
     * @return \Gitlab\HttpClient\Builder
     */
    protected function getBuilder(array $config)
    {
        $builder = new Builder();

        if ($backoff = Arr::get($config, 'backoff')) {
            $builder->addPlugin(new RetryPlugin(['retries' => $backoff === true ? 2 : $backoff]));
        }

        if (Arr::get($config, 'logging')) {
            $builder->addPlugin(new LoggerPlugin($this->logger));
        }

        if (is_array($cache = Arr::get($config, 'cache', false))) {
            $boundedCache = $this->cache->make($cache);

            $builder->addCache(
                new Psr16Adapter($boundedCache),
                ['cache_lifetime' => $boundedCache->getMaximumLifetime()]
            );
        }

        return $builder;
    }
}
