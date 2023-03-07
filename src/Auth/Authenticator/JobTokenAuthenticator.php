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

namespace GrahamCampbell\GitLab\Auth\Authenticator;

use Gitlab\Client;
use InvalidArgumentException;

/**
 * This is the job token authenticator class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class JobTokenAuthenticator extends AbstractAuthenticator
{
    /**
     * Authenticate the client, and return it.
     *
     * @param string[] $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \Gitlab\Client
     */
    public function authenticate(array $config): Client
    {
        $client = $this->getClient();

        if (!array_key_exists('token', $config)) {
            throw new InvalidArgumentException('The job token authenticator requires a token.');
        }

        $client->authenticate($config['token'], Client::AUTH_HTTP_JOB_TOKEN, $config['sudo'] ?? null);

        return $client;
    }
}
