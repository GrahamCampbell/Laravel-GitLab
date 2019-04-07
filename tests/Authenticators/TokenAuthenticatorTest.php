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

use Gitlab\Client;
use GrahamCampbell\GitLab\Authenticators\GitLabAuthenticator;
use GrahamCampbell\Tests\GitLab\AbstractTestCase;
use InvalidArgumentException;
use Mockery;

/**
 * This is the token authenticator test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class TokenAuthenticatorTest extends AbstractTestCase
{
    public function testMakeWithMethod()
    {
        $authenticator = $this->getAuthenticator();

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('authenticate')->once()
            ->with('your-token', 'http_token', null);

        $return = $authenticator->with($client)->authenticate([
            'token'  => 'your-token',
            'method' => 'token',
        ]);

        $this->assertInstanceOf(Client::class, $return);
    }

    public function testMakeWithoutMethod()
    {
        $authenticator = $this->getAuthenticator();

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('authenticate')->once()
            ->with('your-token', 'http_token', null);

        $return = $authenticator->with($client)->authenticate([
            'token'  => 'your-token',
        ]);

        $this->assertInstanceOf(Client::class, $return);
    }

    public function testMakeWithSudo()
    {
        $authenticator = $this->getAuthenticator();

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('authenticate')->once()
            ->with('your-token', 'http_token', 'foo');

        $return = $authenticator->with($client)->authenticate([
            'token'  => 'your-token',
            'method' => 'token',
            'sudo'   => 'foo',
        ]);

        $this->assertInstanceOf(Client::class, $return);
    }

    public function testMakeWithoutToken()
    {
        $authenticator = $this->getAuthenticator();

        $client = Mockery::mock(Client::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The gitlab authenticator requires a token.');

        $authenticator->with($client)->authenticate([]);
    }

    public function testMakeWithoutSettingClient()
    {
        $authenticator = $this->getAuthenticator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The client instance was not given to the authenticator.');

        $authenticator->authenticate([
            'token'  => 'your-token',
            'method' => 'token',
        ]);
    }

    protected function getAuthenticator()
    {
        return new GitLabAuthenticator(Client::AUTH_HTTP_TOKEN);
    }
}
