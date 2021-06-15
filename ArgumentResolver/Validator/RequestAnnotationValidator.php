<?php

namespace Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Validator;

use Doctrine\Common\Annotations\Reader;
use Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Exceptions\ValidateErrorException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Internal\FallbackAttributeReader;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class RequestAnnotationValidator
 * @package Prokl\AnnotatedParamResolverBundle\ArgumentsResolver\Validator
 *
 * @since 01.04.2021
 */
class RequestAnnotationValidator implements RequestAnnotationValidatorInterface
{
    /**
     * @var Reader $reader Читатель аннотаций.
     */
    private $reader;

    /**
     * @var ValidatorInterface $validator Валидатор.
     */
    private $validator;

    /**
     * @var SerializerInterface $serializer Сериалайзер.
     */
    private $serializer;

    /**
     * @var AttributeReader
     */
    private $modernReader;

    /**
     * RequestAnnotationValidator constructor.
     *
     * @param Reader              $reader       Читатель аннотаций.
     * @param ValidatorInterface  $validator    Валидатор.
     * @param SerializerInterface $serializer   Сериалайзер.
     * @param AttributeReader     $modernReader Читатель аннотаций PHP 8.
     */
    public function __construct(
        Reader $reader,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        AttributeReader $modernReader
    ) {
        $this->reader = $reader;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->modernReader = $modernReader;
    }

    /**
     * @inheritDoc
     */
    public function validate($object, string $class) : void
    {
        $validationError = $this->validator($object, $this->getAnnotationsTarget($class));
        if (count($validationError) > 0) {
            throw new ValidateErrorException(
                $this->serializer->serialize($validationError, 'json')
            );
        }
    }

    /**
     * Валидатор.
     *
     * @param object $object                Объект.
     * @param array  $validationConstraints Полученные через аннотации правила.
     *
     * @return array Пустой массив, если все OK.
     */
    private function validator($object, array $validationConstraints) : array
    {
        $result = [];

        foreach ($validationConstraints as $property => $constraints) {
            if (!property_exists($object, $property)) {
                $result[$property][] = 'Property ' . $property . ' not exists.';
                continue;
            }

            $violations = $this->validator->validate($object->{$property}, $constraints);
            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $result[$property][] = $violation->getMessage();
                }
            }
        }

        return $result;
    }

    /**
     * Аннотации валидации.
     *
     * @param string $class Класс.
     *
     * @return array
     * @throws ReflectionException Ошибки рефлексии.
     */
    private function getAnnotationsTarget(string $class) : array
    {
        $reflectionClass = new ReflectionClass($class);
        $props = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
        $annotations = [];
        foreach ($props as $property) {
            // Обработка аннотаций PHP 8.
            $modernAnnotations = $this->modernReader->getPropertyMetadata($property);
            $arModernAnnotation = iterator_to_array($modernAnnotations);

            foreach($arModernAnnotation as $item) {
                $annotations[$property->getName()] = $item;
            }

            if (count($arModernAnnotation) === 0) {
                $annotations[$property->getName()] = $this->reader->getPropertyAnnotations($property);
            }
        }

        return $annotations;
    }
}
