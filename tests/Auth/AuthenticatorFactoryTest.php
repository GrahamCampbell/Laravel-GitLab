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

namespace GrahamCampbell\Tests\GitLab\Auth;

use GrahamCampbell\GitLab\Auth\Authenticator\OauthAuthenticator;
use GrahamCampbell\GitLab\Auth\Authenticator\TokenAuthenticator;
use GrahamCampbell\GitLab\Auth\AuthenticatorFactory;
use GrahamCampbell\Tests\GitLab\AbstractTestCase;
use InvalidArgumentException;
use TypeError;

/**
 * This is the authenticator factory test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class AuthenticatorFactoryTest extends AbstractTestCase
{
    public function testMakeOauthAuthenticator()
    {
        $return = $this->getFactory()->make('oauth');

        $this->assertInstanceOf(OauthAuthenticator::class, $return);
    }

    public function testMakeTokenAuthenticator()
    {
        $return = $this->getFactory()->make('token');

        $this->assertInstanceOf(TokenAuthenticator::class, $return);
    }

    public function testMakeInvalidAuthenticator()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported authentication method [foo].');

        $this->getFactory()->make('foo');
    }

    public function testMakeNoAuthenticator()
    {
        $this->expectException(TypeError::class);

        $this->getFactory()->make(null);
    }

    protected function getFactory()
    {
        return new AuthenticatorFactory();
    }
}
