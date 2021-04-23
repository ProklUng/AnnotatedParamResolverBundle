<?php

namespace Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers;

use Exception;
use Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Exceptions\ValidateErrorException;
use Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Validator\RequestAnnotationValidator;
use Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers\Tools\ExampleRequestClass;
use Prokl\TestingTools\Base\BaseTestCase;
use Symfony\Component\Validator\Validation;

/**
 * Class RequestAnnotationValidatorTest
 * @package Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers
 * @coversDefaultClass RequestAnnotationValidator
 *
 * @since 03.04.2021
 */
class RequestAnnotationValidatorTest extends BaseTestCase
{
    /**
     * @var RequestAnnotationValidator $obTestObject Тестируемый объект.
     */
    protected $obTestObject;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->obTestObject = new RequestAnnotationValidator(
            static::$testContainer->get('annotations.reader'),
            Validation::createValidator(),
            static::$testContainer->get('serializer'),
        );
    }

    /**
     * validate(). Нормальный ход вещей.
     *
     * @return void
     * @throws Exception
     */
    public function testValidate() : void
    {
        $object = new class {
            public $email = 'xxxxxxxxxxxxxxxx';
        };

        $this->obTestObject->validate(
            $object,
            ExampleRequestClass::class
        );

        $this->assertTrue(true);
    }

    /**
     * validate(). Невалидный параметр.
     *
     * @return void
     * @throws Exception
     */
    public function testValidateInvalidValue() : void
    {
        $object = new class {
            public $email = 'x';
        };

        $this->expectException(ValidateErrorException::class);
        $this->obTestObject->validate(
            $object,
            ExampleRequestClass::class
        );
    }

    /**
     * validate(). В параметрах объекта нет валидируемого значения.
     *
     * @return void
     * @throws Exception
     */
    public function testValidateWithoutValue() : void
    {
        $object = new class {
            public $dummy;
        };

        $this->obTestObject->validate(
            $object,
            ExampleRequestClass::class
        );

        $this->assertTrue(true);
    }
}
