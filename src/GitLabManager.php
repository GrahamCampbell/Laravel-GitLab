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

namespace GrahamCampbell\GitLab;

use GrahamCampbell\Manager\AbstractManager;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;

/**
 * This is the gitlab manager class.
 *
 * @method \Gitlab\Client connection(string|null $name)
 * @method \Gitlab\Client reconnect(string|null $name)
 * @method array<string,\Gitlab\Client> getConnections(string $name)
 * @method \Gitlab\Api\DeployKeys deployKeys()
 * @method \Gitlab\Api\Groups groups()
 * @method \Gitlab\Api\Issues issues()
 * @method \Gitlab\Api\IssueBoards issueBoards()
 * @method \Gitlab\Api\Jobs jobs()
 * @method \Gitlab\Api\MergeRequests mergeRequests()
 * @method \Gitlab\Api\Milestones milestones()
 * @method \Gitlab\Api\ProjectNamespaces namespaces()
 * @method \Gitlab\Api\Projects projects()
 * @method \Gitlab\Api\Repositories repositories()
 * @method \Gitlab\Api\RepositoryFiles repositoryFiles()
 * @method \Gitlab\Api\Snippets snippets()
 * @method \Gitlab\Api\SystemHooks systemHooks()
 * @method \Gitlab\Api\Users users()
 * @method \Gitlab\Api\Keys keys()
 * @method \Gitlab\Api\Tags tags()
 * @method \Gitlab\Api\Version version()
 * @method \Gitlab\Api\ApiInterface api(string $name)
 * @method void authenticate(string $token, string|null $authMethod = null, string|null $sudo = null)
 * @method void setUrl(string $url)
 * @method \Http\Client\Common\HttpMethodsClient getHttpClient()
 * @method \Gitlab\HttpClient\Plugin\History getResponseHistory()
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class GitLabManager extends AbstractManager
{
    /**
     * The factory instance.
     *
     * @var \GrahamCampbell\GitLab\GitLabFactory
     */
    protected $factory;

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
    protected function createConnection(array $config)
    {
        return $this->factory->make($config);
    }

    /**
     * Get the configuration name.
     *
     * @return string
     */
    protected function getConfigName()
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
    public function getConnectionConfig(string $name = null)
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
    public function getFactory()
    {
        return $this->factory;
    }
}
