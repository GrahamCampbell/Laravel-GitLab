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

namespace GrahamCampbell\Tests\GitLab\Cache\Connector;

use GrahamCampbell\BoundedCache\BoundedCacheInterface;
use GrahamCampbell\GitLab\Cache\Connector\IlluminateConnector;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Factory;
use InvalidArgumentException;
use Mockery;

/**
 * This is the illuminate connector test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class IlluminateConnectorTest extends AbstractTestCase
{
    public function testConnectStandard(): void
    {
        $cache = Mockery::mock(Factory::class);
        $connector = new IlluminateConnector($cache);
        $cache->shouldReceive('store')->once()->andReturn(Mockery::mock(Repository::class));

        self::assertInstanceOf(BoundedCacheInterface::class, $connector->connect([]));
    }

    public function testConnectFull(): void
    {
        $cache = Mockery::mock(Factory::class);
        $connector = new IlluminateConnector($cache);
        $cache->shouldReceive('store')->once()->with('redis')->andReturn(Mockery::mock(Repository::class));

        $return = $connector->connect([
            'driver'    => 'illuminate',
            'connector' => 'redis',
            'key'       => 'bar',
            'ttl'       => 600,
        ]);

        self::assertInstanceOf(BoundedCacheInterface::class, $return);
    }

    public function testConnectNoCacheFactory(): void
    {
        $connector = new IlluminateConnector();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Illuminate caching support not available.');

        $connector->connect([]);
    }
}
