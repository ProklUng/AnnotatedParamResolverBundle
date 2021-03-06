<?php

namespace Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers;

use Exception;
use InvalidArgumentException;
use Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Exceptions\ValidateErrorException;
use Prokl\AnnotatedParamResolverBundle\ArgumentResolver\QueryParamsArgumentResolver;
use Prokl\AnnotatedParamResolverBundle\Examples\RequestBodyConverted;
use Prokl\AnnotatedParamResolverBundle\Examples\RequestBodyConvertedSpatie;
use Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers\Tools\ContainerAwareBaseTestCase;
use Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers\Tools\SampleController;
use Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers\Tools\SampleControllerMismatched;
use Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers\Traits\ArgumentResolverTrait;
use Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers\Tools\SampleControllerArguments;
use ReflectionException;
use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

/**
 * Class QueryParamsArgumentResolverTest
 * @package Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers
 * @coversDefaultClass QueryParamsArgumentResolver
 *
 * @since 03.04.2021
 */
class QueryParamsArgumentResolverTest extends ContainerAwareBaseTestCase
{
    use ArgumentResolverTrait;

    /**
     * @var QueryParamsArgumentResolver $obTestObject Тестируемый объект.
     */
    protected $obTestObject;

    /**
     * @var string $controllerClass Класс контроллера.
     */
    private $controllerClass = SampleController::class;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->obTestObject = $this->container->get('annotated_bundle_resolvers.query_params');
    }

    /**
     * supports(). Нормальный запрос.
     *
     * @return void
     * @throws Exception
     */
    public function testSupports(): void
    {
        $result = $this->obTestObject->supports(
            $this->createRequest($this->controllerClass, [
                'email' => $this->faker->email,
                'numeric' => $this->faker->numberBetween(1, 100),
            ]),
            $this->getMetaArgument('unserialized', RequestBodyConverted::class)
        );

        $this->assertTrue($result, 'Неправильно определился годный к обработке контроллер');
    }

    /**
     * supports(). Короткая форма аннотации.
     *
     * @return void
     * @throws Exception
     */
    public function testSupportsShortForm(): void
    {
        $result = $this->obTestObject->supports(
            $this->createRequest(
                $this->controllerClass,
                [
                'email' => $this->faker->email,
                'numeric' => $this->faker->numberBetween(1, 100),
                ],
                'GET',
                'actionShort'
            ),
            $this->getMetaArgument('unserialized', RequestBodyConverted::class)
        );

        $this->assertTrue($result, 'Неправильно определился годный к обработке контроллер');
    }

    /**
     * supports(). Mismatched argument.
     *
     * @return void
     * @throws Exception
     */
    public function testSupportsMismatchedArgument(): void
    {
        $request = $this->createRequest(
            SampleControllerMismatched::class,
            [
                'email' => $this->faker->email,
                'numeric' => $this->faker->numberBetween(1, 100),
            ],
            'GET',
            'action2'
        );

        $this->expectException(InvalidArgumentException::class);
        $this->obTestObject->supports(
            $request,
            $this->getMetaArgument('unserialized')
        );
    }

    /**
     * supports(). Нет нужного параметра в Request.
     *
     * @return void
     * @throws Exception
     */
    public function testSupportsNoParam(): void
    {
        $result = $this->obTestObject->supports(
            $this->createRequest($this->controllerClass, [
                'email' => $this->faker->email,
                'numeric' => $this->faker->numberBetween(1, 100),
            ]),
            $this->getMetaArgument('unknown')
        );

        $this->assertFalse(
            $result,
            'Неправильно определился контроллер с отсутствующим параметром'
        );
    }

    /**
     * supports(). Не GET запрос.
     *
     * @return void
     * @throws Exception
     */
    public function testSupportsNoGetQuery(): void
    {
        $result = $this->obTestObject->supports(
            $this->createRequest(
                $this->controllerClass,
                [
                'email' => $this->faker->email,
                'numeric' => $this->faker->numberBetween(1, 100),
                ],
                'POST'
            ),
            $this->getMetaArgument('unserialized')
        );

        $this->assertFalse(
            $result,
            'Неправильно определился негодный к обработке тип запроса'
        );
    }

    /**
     * supports(). Контроллер без аннотации.
     *
     * @return void
     * @throws Exception
     */
    public function testSupportsNoAnnotations(): void
    {
        $result = $this->obTestObject->supports(
            $this->createRequest(SampleControllerArguments::class, [
                'email' => $this->faker->email,
                'numeric' => $this->faker->numberBetween(1, 100),
            ]),
            $this->getMetaArgument('unserialized')
        );

        $this->assertFalse(
            $result,
            'Неправильно определился негодный к обработке тип запроса'
        );
    }

    /**
     * resolve().
     *
     * @return void
     * @throws ValidateErrorException
     * @throws ReflectionException
     */
    public function testResolve(): void
    {
        $email = $this->faker->email;
        $numeric = $this->faker->numberBetween(1, 100);

        $request = $this->createRequest(
            $this->controllerClass,
            [
                'email' => $email,
                'numeric' => $numeric,
            ]
        );

        $result = current(iterator_to_array($this->obTestObject->resolve(
            $request,
            $this->getMetaArgument('unserialized', RequestBodyConverted::class)
        )));

        $this->assertSame(
            $email,
            $result->email,
            'Поле не обработано верно'
        );

        $this->assertSame(
            $numeric,
            $result->numeric,
            'Поле не обработано верно'
        );
    }

    /**
     * resolve(). Spatie DTO.
     *
     * @return void
     * @throws ValidateErrorException | ReflectionException
     */
    public function testResolveSpatieDTO(): void
    {
        $email = $this->faker->email;
        $numeric = $this->faker->numberBetween(1, 100);

        $request = $this->createRequest(
            $this->controllerClass,
            [
                'email' => $email,
                'numeric' => $numeric,
            ],
            'GET',
            'actionSpatie'
        );

        $result = current(iterator_to_array($this->obTestObject->resolve(
            $request,
            $this->getMetaArgument('unserialized', RequestBodyConvertedSpatie::class)
        )));

        $this->assertInstanceOf(
            DataTransferObject::class,
            $result
        );

        $this->assertSame(
            $email,
            $result->email,
            'Поле не обработано верно'
        );

        $this->assertSame(
            $numeric,
            $result->numeric,
            'Поле не обработано верно'
        );
    }

    /**
     * resolve(). Проверка кастинга переменных в запросе.
     *
     * @return void
     * @throws ValidateErrorException
     * @throws ReflectionException
     */
    public function testResolveCastingQuery(): void
    {
        $email = $this->faker->email;
        $numeric = (string)$this->faker->numberBetween(1, 100);

        $request = $this->createRequest(
            $this->controllerClass,
            [
                'email' => $email,
                'numeric' => $numeric,
            ]
        );

        $result = current(iterator_to_array($this->obTestObject->resolve(
            $request,
            $this->getMetaArgument('unserialized', RequestBodyConverted::class)
        )));

        $this->assertSame(
            $email,
            $result->email,
            'Поле не обработано верно'
        );

        $this->assertSame(
            (int)$numeric,
            $result->numeric,
            'Поле не обработано верно'
        );
    }

    /**
     * resolve(). Проверка вызова валидатора.
     *
     * @return void
     * @throws ValidateErrorException | ReflectionException
     */
    public function testResolveCallValidation(): void
    {
        $this->obTestObject = new QueryParamsArgumentResolver(
            static::$testContainer->get('annotated_bundle_resolvers.psr6_selective_reader'),
            static::$testContainer->get(ControllerResolver::class),
            static::$testContainer->get('serializer'),
            $this->getMockValidator(true),
            static::$testContainer->get('property_info')
        );

        $request = $this->createRequest(
            $this->controllerClass,
            [
                'email' => $this->faker->email,
                'numeric' => $this->faker->numberBetween(1, 100),
            ]
        );

        // Проверка на то, что исключение не выбрасывается.
        $result = iterator_to_array($this->obTestObject->resolve(
            $request,
            $this->getMetaArgument('unserialized', RequestBodyConverted::class)
        ));

        $this->assertTrue(true);
    }

    /**
     * resolve(). Проверка обработки опции validate.
     *
     * @return void
     * @throws ValidateErrorException | ReflectionException
     */
    public function testResolveValidationOption(): void
    {
        $this->obTestObject = new QueryParamsArgumentResolver(
            static::$testContainer->get('annotated_bundle_resolvers.psr6_selective_reader'),
            static::$testContainer->get(ControllerResolver::class),
            static::$testContainer->get('serializer'),
            $this->getMockValidator(false),
            static::$testContainer->get('property_info')
        );

        $request = $this->createRequest(
            $this->controllerClass,
            [
                'email' => $this->faker->email,
                'numeric' => $this->faker->numberBetween(1, 100),
            ],
            'GET',
            'actionNoValidate'
        );

        $result = iterator_to_array($this->obTestObject->resolve(
            $request,
            $this->getMetaArgument('unserialized', RequestBodyConverted::class)
        ));

        $this->assertTrue(true);
    }
}
