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

namespace GrahamCampbell\Tests\GitLab\Authenticators;

use GrahamCampbell\GitLab\Authenticators\AuthenticatorFactory;
use GrahamCampbell\GitLab\Authenticators\GitLabAuthenticator;
use GrahamCampbell\Tests\GitLab\AbstractTestCase;

/**
 * This is the authenticator factory test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class AuthenticatorFactoryTest extends AbstractTestCase
{
    public function testMakeOauthAuthenticator()
    {
        $return = $this->getFactory()->make('oauth');

        $this->assertInstanceOf(GitLabAuthenticator::class, $return);
    }

    public function testMakeTokenAuthenticator()
    {
        $return = $this->getFactory()->make('token');

        $this->assertInstanceOf(GitLabAuthenticator::class, $return);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unsupported authentication method [foo].
     */
    public function testMakeInvalidAuthenticator()
    {
        $this->getFactory()->make('foo');
    }

    /**
     * @expectedException TypeError
     */
    public function testMakeNoAuthenticator()
    {
        $this->getFactory()->make(null);
    }

    protected function getFactory()
    {
        return new AuthenticatorFactory();
    }
}
