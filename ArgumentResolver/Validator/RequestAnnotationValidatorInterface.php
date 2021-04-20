<?php

namespace Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Validator;

use Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Exceptions\ValidateErrorException;
use ReflectionException;

/**
 * Interface RequestAnnotationValidatorInterface
 * @package Prokl\AnnotatedParamResolverBundle\ArgumentsResolver\Validator
 *
 * @since 01.04.2021
 */
interface RequestAnnotationValidatorInterface
{
    /**
     * @param object $object Объект, подлежащий валидации.
     * @param string $class  Класс.
     *
     * @return void
     * @throws ReflectionException    Ошибки рефлексии.
     * @throws ValidateErrorException Ошибки валидации.
     */
    public function validate($object, string $class) : void;
}
