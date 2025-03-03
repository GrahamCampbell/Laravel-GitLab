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

namespace GrahamCampbell\GitLab\Auth;

use GrahamCampbell\GitLab\Auth\Authenticator\AuthenticatorInterface;
use InvalidArgumentException;

/**
 * This is the authenticator factory class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class AuthenticatorFactory
{
    /**
     * Make a new authenticator instance.
     *
     * @param string $method
     *
     * @throws \InvalidArgumentException
     *
     * @return \GrahamCampbell\GitLab\Auth\Authenticator\AuthenticatorInterface
     */
    public function make(string $method): AuthenticatorInterface
    {
        return match ($method) {
            'job_token' => new Authenticator\JobTokenAuthenticator(),
            'oauth' => new Authenticator\OauthAuthenticator(),
            'token' => new Authenticator\TokenAuthenticator(),
            default => throw new InvalidArgumentException("Unsupported authentication method [$method]."),
        };
    }
}
