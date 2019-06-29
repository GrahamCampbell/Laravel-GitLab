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

use InvalidArgumentException;

/**
 * This is the gitlab authenticator class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class GitLabAuthenticator extends AbstractAuthenticator
{
    /**
     * The auth method to use.
     *
     * @var string
     */
    protected $method;

    /**
     * Create a new gitlab authenticator instance.
     *
     * @param string $method
     *
     * @return void
     */
    public function __construct(string $method)
    {
        $this->method = $method;
    }

    /**
     * Authenticate the client, and return it.
     *
     * @param string[] $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \Gitlab\Client
     */
    public function authenticate(array $config)
    {
        if (!$this->client) {
            throw new InvalidArgumentException('The client instance was not given to the authenticator.');
        }

        if (!array_key_exists('token', $config)) {
            throw new InvalidArgumentException('The gitlab authenticator requires a token.');
        }

        $this->client->authenticate($config['token'], $this->method, $config['sudo'] ?? null);

        return $this->client;
    }
}
