<?php

namespace Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Exceptions;

use Prokl\AnnotatedParamResolverBundle\Exceptions\BaseException;
use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;

/**
 * Class ValidateErrorException
 * @package Prokl\AnnotatedParamResolverBundle\ArgumentsResolver\Exceptions
 *
 * @sinсe 01.04.2021
 */
class ValidateErrorException extends BaseException implements RequestExceptionInterface
{

}
