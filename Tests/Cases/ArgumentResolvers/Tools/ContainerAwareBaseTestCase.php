<?php

namespace Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers\Tools;

use Exception;
use Prokl\TestingTools\Base\BaseTestCase;
use Prokl\TestingTools\Tools\Container\BuildContainer;

/**
 * Class ContainerAwareBaseTestCase
 * @package Cases\ArgumentResolvers\Tools
 *
 * @since 23.04.2021
 */
class ContainerAwareBaseTestCase extends BaseTestCase
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->container = static::$testContainer = BuildContainer::getTestContainer(
            [
                'dev/test_container.yaml',
                'annotations.yaml',
                'resolvers.yaml',
                'dev/local.yaml'
            ],
            '/Resources/config'
        );

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        BuildContainer::rrmdir($_SERVER['DOCUMENT_ROOT'] . 'Tests/Cases/ArgumentResolvers/Tools/cache');
    }
}
