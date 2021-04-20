<?php

namespace Prokl\AnnotatedParamResolverBundle\Tests\Tools;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BaseTestCase
 * @package Prokl\AnnotatedParamResolverBundle\Tests\Tools
 *
 * @since 05.12.2020
 */
class BaseTestCase extends TestCase
{
    use ExceptionAsserts;
    use PHPUnitTrait;

    /**
     * @var mixed $testObject Тестируемый объект.
     */
    protected $testObject;

    /**
     * @var Generator | null $faker
     */
    protected $faker;

    /**
     * @var ContainerInterface $testContainer Тестовый контейнер.
     */
    protected static $testContainer;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        // Инициализация тестового контейнера.
        static::$testContainer = container()->get('annotated_bundle_resolvers.test.service_container')
            ?: container();

        Mockery::resetContainer();
        parent::setUp();

        $this->faker = Factory::create();
    }

    protected function tearDown(): void
    {
        // Сбросить тестовый контейнер.
        static::$testContainer->reset();

        parent::tearDown();

        Mockery::close();

        $this->testObject = null;
    }
}
