<?php

namespace Prokl\AnnotatedParamResolverBundle;

use Prokl\AnnotatedParamResolverBundle\DependencyInjection\AnnotatedParamResolverExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AnnotatedParamResolverBundle
 * @package Prokl\AnnotatedParamResolverBundle
 *
 * @since 20.04.2021
 */
class AnnotatedParamResolverBundle extends Bundle
{
   /**
   * @inheritDoc
   */
    public function getContainerExtension()
    {
        if ($this->extension === null) {
            $this->extension = new AnnotatedParamResolverExtension();
        }

        return $this->extension;
    }
}
