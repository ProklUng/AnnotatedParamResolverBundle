<?php

namespace Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Exceptions;

use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use Prokl\BaseException\BaseException;

/**
 * Class ValidateErrorException
 * @package Prokl\AnnotatedParamResolverBundle\ArgumentsResolver\Exceptions
 *
 * @sinсe 01.04.2021
 */
class ValidateErrorException extends BaseException implements RequestExceptionInterface
{

}
