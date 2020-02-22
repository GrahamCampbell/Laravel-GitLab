<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitHub.
 *
 * (c) Graham Campbell <graham@alt-three.com>
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
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;

/**
 * This is the service provider test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    public function testAuthFactoryIsInjectable()
    {
        $this->assertIsInjectable(AuthenticatorFactory::class);
    }

    public function testCacheFactoryIsInjectable()
    {
        $this->assertIsInjectable(ConnectionFactory::class);
    }

    public function testGitLabFactoryIsInjectable()
    {
        $this->assertIsInjectable(GitLabFactory::class);
    }

    public function testGitLabManagerIsInjectable()
    {
        $this->assertIsInjectable(GitLabManager::class);
    }

    public function testBindings()
    {
        $this->assertIsInjectable(Client::class);

        $original = $this->app['gitlab.connection'];
        $this->app['gitlab']->reconnect();
        $new = $this->app['gitlab.connection'];

        $this->assertNotSame($original, $new);
        $this->assertEquals($original, $new);
    }
}
