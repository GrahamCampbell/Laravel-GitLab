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

namespace GrahamCampbell\GitLab\Authenticators;

use Gitlab\Client;
use InvalidArgumentException;

/**
 * This is the authenticator factory class.
 *
 * @author Graham Campbell <graham@alt-three.com>
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
     * @return \GrahamCampbell\GitLab\Authenticators\AuthenticatorInterface
     */
    public function make(string $method)
    {
        switch ($method) {
            case 'oauth':
                return new GitLabAuthenticator(Client::AUTH_OAUTH_TOKEN);
            case 'token':
                return new GitLabAuthenticator(Client::AUTH_HTTP_TOKEN);
        }

        throw new InvalidArgumentException("Unsupported authentication method [$method].");
    }
}
