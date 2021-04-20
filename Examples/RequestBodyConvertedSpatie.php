<?php

namespace Prokl\AnnotatedParamResolverBundle\Examples;

use Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Contracts\UnserializableRequestInterface;
use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RequestBodyConvertedSpatie
 * @package Prokl\AnnotatedParamResolverBundle\Examples
 *
 * @since 03.04.2021
 */
class RequestBodyConvertedSpatie extends DataTransferObject implements UnserializableRequestInterface
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
