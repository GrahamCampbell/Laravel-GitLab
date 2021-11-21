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

/**
 * This is the abstract authenticator class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
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
     * @return \GrahamCampbell\GitLab\Auth\Authenticator\AuthenticatorInterface
     */
    public function with(Client $client)
    {
        $this->client = $client;

        return $this;
    }
}
