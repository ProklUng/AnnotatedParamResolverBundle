<?php

namespace Prokl\AnnotatedParamResolverBundle\Examples;

use Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Contracts\UnserializableRequestInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RequestBodyConverted
 * @package Prokl\AnnotatedParamResolverBundle\Examples
 *
 * @since 01.04.2021
 */
class RequestBodyConverted implements UnserializableRequestInterface
{
    /**
     * @var string $email
     *
     * @Assert\Length(
     *  min=3,
     *  minMessage="Email must be at least {{ limit }} characters long"
     * )
     */
    public $email;

    /**
     * @var integer
     */
    public $numeric;
}
