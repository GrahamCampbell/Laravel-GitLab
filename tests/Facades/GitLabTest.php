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

namespace GrahamCampbell\Tests\GitLab\Facades;

use GrahamCampbell\GitLab\Facades\GitLab;
use GrahamCampbell\GitLab\GitLabManager;
use GrahamCampbell\TestBenchCore\FacadeTrait;
use GrahamCampbell\Tests\GitLab\AbstractTestCase;

/**
 * This is the gitlab facade test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class GitLabTest extends AbstractTestCase
{
    use FacadeTrait;

    /**
     * Get the facade accessor.
     *
     * @return string
     */
    protected function getFacadeAccessor()
    {
        return 'gitlab';
    }

    /**
     * Get the facade class.
     *
     * @return string
     */
    protected function getFacadeClass()
    {
        return GitLab::class;
    }

    /**
     * Get the facade root.
     *
     * @return string
     */
    protected function getFacadeRoot()
    {
        return GitLabManager::class;
    }
}
