<?php

namespace Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers\Tools;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ExampleRequestClassAnno
 * @package Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers\Tools
 *
 * @since 15.06.2021
 */
class ExampleRequestClassAnno
{
    #[Assert\Length(min: 3,minMessage : "Email must be at least {{ limit }} characters long")]
    /**
     * @var string $email
     *
     */
    public $email;
}
