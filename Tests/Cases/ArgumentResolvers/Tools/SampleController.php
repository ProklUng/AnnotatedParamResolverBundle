<?php

namespace Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers\Tools;

use Prokl\AnnotatedParamResolverBundle\Annotation\QueryParams;
use Prokl\AnnotatedParamResolverBundle\Examples\RequestBodyConverted;
use Prokl\AnnotatedParamResolverBundle\Examples\RequestBodyConvertedSpatie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SampleController
 * @package Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers\Tools
 *
 * @since 03.04.2021
 */
class SampleController extends AbstractController
{
    /**
     * Controller
     *
     * Параметры аннотации необязательны!
     *
     * @param RequestBodyConverted $unserialized
     *
     * @return JsonResponse $content
     * @QueryParams(
            var="unserialized",
            class="Prokl\AnnotatedParamResolverBundle\Examples\RequestBodyConverted",
            validate=true
        )
     */
    public function action(
        RequestBodyConverted $unserialized
    ): JsonResponse {
        return new JsonResponse();
    }

    /**
     *
     * @param RequestBodyConverted $unserialized
     *
     * @return JsonResponse $content
     * @QueryParams(
            var="unserialized",
            class="Prokl\AnnotatedParamResolverBundle\Examples\RequestBodyConverted",
            validate=false
        )
     */
    public function actionNoValidate(
        RequestBodyConverted $unserialized
    ): JsonResponse {
        return new JsonResponse();
    }

    /**
     * @param RequestBodyConverted $unserialized
     *
     * @return JsonResponse $content
     * @QueryParams()
     */
    public function actionShort(
        RequestBodyConverted $unserialized
    ): JsonResponse {
        return new JsonResponse();
    }

    /**
     * Controller
     *
     * Параметры аннотации необязательны!
     *
     * @param RequestBodyConvertedSpatie $unserialized
     *
     * @return JsonResponse $content
     * @QueryParams(
            var="unserialized",
            class="Prokl\AnnotatedParamResolverBundle\Examples\RequestBodyConvertedSpatie",
          validate=true
    )
     */
    public function actionSpatie(
        RequestBodyConvertedSpatie $unserialized
    ): JsonResponse {
        return new JsonResponse();
    }
}
