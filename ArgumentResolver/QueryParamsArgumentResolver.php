<?php

namespace Prokl\AnnotatedParamResolverBundle\ArgumentResolver;

use InvalidArgumentException;
use ReflectionException;
use Prokl\AnnotatedParamResolverBundle\Annotation\QueryParams;
use Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Contracts\UnserializableRequestInterface;
use Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Exceptions\ValidateErrorException;
use Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Traits\ArgumentResolverTrait;
use Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Validator\RequestAnnotationValidatorInterface;
use Spiral\Attributes\ReaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class QueryParamsArgumentResolver
 * @package Prokl\AnnotatedParamResolverBundle\ArgumentsResolver
 *
 * @description
 *
 * Аннотация метода контроллера - @QueryParams.  Или PHP 8 атрибут QueryParams.
 *
 * Параметры (не обязательные)
 * var - название переменной в action контроллера,
 * class - класс переменной в action контроллера.
 * Если не указано ни того, ни другого, то ресолвер проверяет - не реализует ли класс
 * интерфейс UnserializableRequestInterface. Если да, то этот аргумент - наш клиент.
 * Рефлексией берет название переменной.
 * Параметр validate = true/false. Валидировать через аннотации. По умолчанию - да.
 *
 * @since 01.04.2021
 */
final class QueryParamsArgumentResolver implements ArgumentValueResolverInterface
{
    use ArgumentResolverTrait;

    private const DEFAULT_ANNOTATION = QueryParams::class;

    /**
     * @var ReaderInterface $reader Читатель аннотаций.
     */
    private $reader;

    /**
     * @var ControllerResolverInterface $controllerResolver Controller Resolver.
     */
    private $controllerResolver;

    /**
     * @var SerializerInterface $serializer Сериалайзер.
     */
    private $serializer;

    /**
     * @var RequestAnnotationValidatorInterface $validator
     */
    private $validator;

    /**
     * @var PropertyInfoExtractor $extractor
     */
    private $extractor;

    /**
     * QueryParamsArgumentResolver constructor.
     *
     * @param ReaderInterface                     $reader             Читатель аннотаций.
     * @param ControllerResolverInterface         $controllerResolver Controller Resolver.
     * @param SerializerInterface                 $serializer         Сериалайзер.
     * @param RequestAnnotationValidatorInterface $validator          Валидатор.
     * @param PropertyInfoExtractor               $extractor          Property extractor.
     */
    public function __construct(
        ReaderInterface $reader,
        ControllerResolverInterface $controllerResolver,
        SerializerInterface $serializer,
        RequestAnnotationValidatorInterface $validator,
        PropertyInfoExtractor $extractor
    ) {
        $this->reader = $reader;
        $this->controllerResolver = $controllerResolver;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->extractor = $extractor;
    }

    /**
     * @inheritDoc
     * @throws ReflectionException      Ошибки рефлексии.
     * @throws InvalidArgumentException Type mismatch.
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $annotation = $this->getAnnotation($request, self::DEFAULT_ANNOTATION);

        if (!$annotation instanceof QueryParams
            || $request->getMethod() !== 'GET'
            || count($request->query->all()) === 0
        ) {
            return false;
        }

        $variable = $annotation->getVar() ?: $argument->getName();
        if ($argument->getName() !== $variable) {
            return false;
        }

        if (!class_exists($argument->getType())) {
            $this->throwMismatchException($argument->getName(), $argument->getType());
        }

        // Проверка на интерфейс, если не задан класс напрямую в аннотации.
        $interfaces = class_implements($argument->getType());
        if (!$annotation->getClass()
            && !in_array(UnserializableRequestInterface::class, $interfaces, true)
        ) {
            $this->throwMismatchImplementedInterface($argument->getName());
        }

        return true;
    }

    /**
     * @inheritDoc
     * @throws ReflectionException    Ошибки рефлексии.
     * @throws ValidateErrorException Ошибки валидации.
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $annotation = $this->getAnnotation($request, self::DEFAULT_ANNOTATION);
        $class = $annotation->getClass() ?: $argument->getType();

        $castQuery = $this->castRequest($request->query->all(), $class);
        // Если класс наследует Spatie DTO, то инстанцируем его иным образом.
        if ($this->isSpatieDto($class)) {
            $object = new $class($castQuery);
        } else {
            $object = $this->serializer->denormalize($castQuery, $class, null, []);
        }

        if ($annotation->isValidate()) {
            $this->validator->validate($object, $class);
        }

        $variable = $annotation->getVar() ?: $argument->getName();
        $request->attributes->set($variable, $object);

        yield $object;
    }
}
