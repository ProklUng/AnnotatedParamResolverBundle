<?php

namespace Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers\Tools;

use Prokl\AnnotatedParamResolverBundle\Examples\RequestBodyConverted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Prokl\AnnotatedParamResolverBundle\Annotation\RequestBody;

/**
 * Class SampleControllerBodyAnno
 * @package Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers\Tools
 *
 * @since 15.06.2021
 */
class SampleControllerBodyAnno extends AbstractController
{
    #[RequestBody(var: 'unserialized', class: 'Prokl\AnnotatedParamResolverBundle\Examples\RequestBodyConverted', validate:true)]
    public function action(
        RequestBodyConverted $unserialized
    ): JsonResponse {
        return new JsonResponse();
    }

    /**
     * Параметры аннотации необязательны!
     *
     * @param RequestBodyConverted $unserialized
     *
     * @return JsonResponse $content
     */
    #[RequestBody]
    public function actionNoValidate(
        RequestBodyConverted $unserialized
    ): JsonResponse {
        return new JsonResponse();
    }

    /**
     * @param RequestBodyConverted $unserialized
     *
     * @return JsonResponse $content
     */
    #[RequestBody]
    public function actionShort(
        RequestBodyConverted $unserialized
    ): JsonResponse {
        return new JsonResponse();
    }
}
