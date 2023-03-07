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

namespace GrahamCampbell\Tests\GitLab\Auth\Authenticators;

use Gitlab\Client;
use GrahamCampbell\GitLab\Auth\Authenticator\JobTokenAuthenticator;
use GrahamCampbell\Tests\GitLab\AbstractTestCase;
use InvalidArgumentException;
use Mockery;

/**
 * This is the job token authenticator test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class JobTokenAuthenticatorTest extends AbstractTestCase
{
    public function testMakeWithMethod(): void
    {
        $authenticator = new JobTokenAuthenticator();

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('authenticate')->once()
            ->with('your-token', 'http_job_token', null);

        $return = $authenticator->with($client)->authenticate([
            'token'  => 'your-token',
            'method' => 'token',
        ]);

        self::assertInstanceOf(Client::class, $return);
    }

    public function testMakeWithoutMethod(): void
    {
        $authenticator = new JobTokenAuthenticator();

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('authenticate')->once()
            ->with('your-token', 'http_job_token', null);

        $return = $authenticator->with($client)->authenticate([
            'token'  => 'your-token',
        ]);

        self::assertInstanceOf(Client::class, $return);
    }

    public function testMakeWithSudo(): void
    {
        $authenticator = new JobTokenAuthenticator();

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('authenticate')->once()
            ->with('your-token', 'http_job_token', 'foo');

        $return = $authenticator->with($client)->authenticate([
            'token'  => 'your-token',
            'method' => 'token',
            'sudo'   => 'foo',
        ]);

        self::assertInstanceOf(Client::class, $return);
    }

    public function testMakeWithoutToken(): void
    {
        $authenticator = new JobTokenAuthenticator();

        $client = Mockery::mock(Client::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The job token authenticator requires a token.');

        $authenticator->with($client)->authenticate([]);
    }

    public function testMakeWithoutSettingClient(): void
    {
        $authenticator = new JobTokenAuthenticator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The client instance was not given to the authenticator.');

        $authenticator->authenticate([
            'token'  => 'your-token',
            'method' => 'token',
        ]);
    }
}
