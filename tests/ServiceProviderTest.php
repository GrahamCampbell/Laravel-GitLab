<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitHub.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\GitLab;

use Gitlab\Client;
use GrahamCampbell\GitLab\Auth\AuthenticatorFactory;
use GrahamCampbell\GitLab\Cache\ConnectionFactory;
use GrahamCampbell\GitLab\GitLabFactory;
use GrahamCampbell\GitLab\GitLabManager;
use GrahamCampbell\GitLab\HttpClient\BuilderFactory;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;

/**
 * This is the service provider test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    public function testHttpClientFactoryIsInjectable(): void
    {
        $this->assertIsInjectable(BuilderFactory::class);
    }

    public function testAuthFactoryIsInjectable(): void
    {
        $this->assertIsInjectable(AuthenticatorFactory::class);
    }

    public function testCacheFactoryIsInjectable(): void
    {
        $this->assertIsInjectable(ConnectionFactory::class);
    }

    public function testGitLabFactoryIsInjectable(): void
    {
        $this->assertIsInjectable(GitLabFactory::class);
    }

    public function testGitLabManagerIsInjectable(): void
    {
        $this->assertIsInjectable(GitLabManager::class);
    }

    public function testBindings(): void
    {
        $this->assertIsInjectable(Client::class);

        $original = $this->app['gitlab.connection'];
        $this->app['gitlab']->reconnect();
        $new = $this->app['gitlab.connection'];

        self::assertNotSame($original, $new);
        self::assertEquals($original, $new);
    }
}
