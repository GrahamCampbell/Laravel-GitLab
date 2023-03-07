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

namespace GrahamCampbell\GitLab;

use Gitlab\Client;
use GrahamCampbell\Manager\AbstractManager;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;

/**
 * This is the gitlab manager class.
 *
 * @method \Gitlab\Client                                 connection(string|null $name = null)
 * @method \Gitlab\Client                                 reconnect(string|null $name = null)
 * @method void                                           disconnect(string|null $name = null)
 * @method array<string,\Gitlab\Client>                   getConnections()
 * @method \Gitlab\Api\DeployKeys                         deployKeys()
 * @method \Gitlab\Api\Deployments                        deployments()
 * @method \Gitlab\Api\Environments                       environments()
 * @method \Gitlab\Api\Groups                             groups()
 * @method \Gitlab\Api\GroupsBoards                       groupsBoards()
 * @method \Gitlab\Api\GroupsEpics                        groupsEpics()
 * @method \Gitlab\Api\GroupsMilestones                   groupsMilestones()
 * @method \Gitlab\Api\IssueBoards                        issueBoards()
 * @method \Gitlab\Api\IssueLinks                         issueLinks()
 * @method \Gitlab\Api\Issues                             issues()
 * @method \Gitlab\Api\IssuesStatistics                   issuesStatistics()
 * @method \Gitlab\Api\Jobs                               jobs()
 * @method \Gitlab\Api\Keys                               keys()
 * @method \Gitlab\Api\MergeRequests                      mergeRequests()
 * @method \Gitlab\Api\Milestones                         milestones()
 * @method \Gitlab\Api\ProjectNamespaces                  namespaces()
 * @method \Gitlab\Api\Projects                           projects()
 * @method \Gitlab\Api\Repositories                       repositories()
 * @method \Gitlab\Api\RepositoryFiles                    repositoryFiles()
 * @method \Gitlab\Api\Schedules                          schedules()
 * @method \Gitlab\Api\Snippets                           snippets()
 * @method \Gitlab\Api\SystemHooks                        systemHooks()
 * @method \Gitlab\Api\Users                              users()
 * @method \Gitlab\Api\Tags                               tags()
 * @method \Gitlab\Api\Version                            version()
 * @method \Gitlab\Api\Wiki                               wiki()
 * @method \Gitlab\Api\ApiInterface                       api(string $name)
 * @method void                                           authenticate(string $token, string $authMethod, string|null $sudo = null)
 * @method void                                           setUrl(string $url)
 * @method \Psr\Http\Message\ResponseInterface|null       getLastResponse()
 * @method \Gitlab\HttpClient\Plugin\History              getResponseHistory()
 * @method \Http\Client\Common\HttpMethodsClientInterface getHttpClient()
 * @method \Http\Message\StreamFactory                    getStreamFactory()
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class GitLabManager extends AbstractManager
{
    /**
     * The factory instance.
     *
     * @var \GrahamCampbell\GitLab\GitLabFactory
     */
    protected GitLabFactory $factory;

    /**
     * Create a new gitlab manager instance.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \GrahamCampbell\GitLab\GitLabFactory    $factory
     *
     * @return void
     */
    public function __construct(Repository $config, GitLabFactory $factory)
    {
        parent::__construct($config);
        $this->factory = $factory;
    }

    /**
     * Create the connection instance.
     *
     * @param array $config
     *
     * @return \Gitlab\Client
     */
    protected function createConnection(array $config): Client
    {
        return $this->factory->make($config);
    }

    /**
     * Get the configuration name.
     *
     * @return string
     */
    protected function getConfigName(): string
    {
        return 'gitlab';
    }

    /**
     * Get the configuration for a connection.
     *
     * @param string|null $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getConnectionConfig(string $name = null): array
    {
        $config = parent::getConnectionConfig($name);

        if (is_string($cache = Arr::get($config, 'cache'))) {
            $config['cache'] = $this->getNamedConfig('cache', 'Cache', $cache);
        }

        return $config;
    }

    /**
     * Get the factory instance.
     *
     * @return \GrahamCampbell\GitLab\GitLabFactory
     */
    public function getFactory(): GitLabFactory
    {
        return $this->factory;
    }
}
