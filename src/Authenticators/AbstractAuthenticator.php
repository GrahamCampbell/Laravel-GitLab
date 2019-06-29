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

/**
 * This is the abstract authenticator class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
abstract class AbstractAuthenticator implements AuthenticatorInterface
{
    /**
     * The client to perform the authentication on.
     *
     * @var \Gitlab\Client|null
     */
    protected $client;

    /**
     * Set the client to perform the authentication on.
     *
     * @param \Gitlab\Client $client
     *
     * @return \GrahamCampbell\GitLab\Authenticators\AuthenticatorInterface
     */
    public function with(Client $client)
    {
        $this->client = $client;

        return $this;
    }
}
