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

namespace GrahamCampbell\GitLab\Http;

use Gitlab\HttpClient\Builder;
use GrahamCampbell\CachePlugin\CachePlugin;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin\Cache\Generator\CacheKeyGenerator;
use Http\Client\Common\PluginClient;
use Psr\Cache\CacheItemPoolInterface;
use ReflectionClass;

/**
 * This is the client builder class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ClientBuilder extends Builder
{
    /**
     * The cache plugin to use.
     *
     * @var \GrahamCampbell\CachePlugin\CachePlugin|null
     */
    protected $cachePlugin;

    /**
     * Get the gitlab http client.
     *
     * @return \Http\Client\Common\HttpMethodsClient
     */
    public function getHttpClient()
    {
        if ($this->getProperty('httpClientModified')->getValue($this)) {
            $this->getProperty('httpClientModified')->setValue($this, false);
            $this->getProperty('pluginClient')->setValue($this, new HttpMethodsClient(
                new PluginClient($this->getProperty('httpClient')->getValue($this), $this->getPlugins()),
                $this->getProperty('requestFactory')->getValue($this)
            ));
        }

        return $this->getProperty('pluginClient')->getValue($this);
    }

    /**
     * Get the plugins to use.
     *
     * @return \Http\Client\Common\Plugin[]
     */
    protected function getPlugins()
    {
        $plugins = $this->getProperty('plugins')->getValue($this);

        if ($this->cachePlugin) {
            $plugins[] = $this->cachePlugin;
        }

        return $plugins;
    }

    /**
     * Add a cache plugin to cache responses locally.
     *
     * @param \Psr\Cache\CacheItemPoolInterface $cachePool
     * @param array                             $config
     *
     * @return void
     */
    public function addCache(CacheItemPoolInterface $cachePool, array $config = [])
    {
        $this->setCachePlugin($cachePool, $config['generator'] ?? null, $config['lifetime'] ?? null);

        $this->getProperty('httpClientModified')->setValue($this, true);
    }

    /**
     * Add a cache plugin to cache responses locally.
     *
     * @param \Psr\Cache\CacheItemPoolInterface                                 $cachePool
     * @param \Http\Client\Common\Plugin\Cache\Generator\CacheKeyGenerator|null $generator
     * @param int|null                                                          $lifetime
     *
     * @return void
     */
    protected function setCachePlugin(CacheItemPoolInterface $cachePool, CacheKeyGenerator $generator = null, int $lifetime = null)
    {
        $stream = $this->getProperty('streamFactory')->getValue($this);

        $this->cachePlugin = new CachePlugin($cachePool, $stream, $generator, $lifetime);
    }

    /**
     * Get the builder reflection property for the given name.
     *
     * @param string $name
     *
     * @return \ReflectionProperty
     */
    protected function getProperty(string $name)
    {
        $prop = (new ReflectionClass(Builder::class))->getProperty($name);

        $prop->setAccessible(true);

        return $prop;
    }
}
