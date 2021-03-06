<?php

namespace Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers\Tools;

use Prokl\AnnotatedParamResolverBundle\Annotation\QueryParams;
use Prokl\AnnotatedParamResolverBundle\Annotation\RequestBody;
use Prokl\AnnotatedParamResolverBundle\Annotation\RequestParams;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SampleControllerMismatched
 * @package Prokl\AnnotatedParamResolverBundle\Tests\Cases\ArgumentResolvers\Tools
 *
 * @since 03.04.2021
 */
class SampleControllerMismatched extends AbstractController
{
    /**
     * Параметры аннотации необязательны!
     *
     * @param string $unserialized
     *
     * @return JsonResponse $content
     * @RequestParams(
            var="unserialized",
            class="Prokl\AnnotatedParamResolverBundle\Examples\RequestBodyConverted",
            validate=true
        )
     */
    public function action(
        string $unserialized
    ): JsonResponse {
        return new JsonResponse();
    }

    /**
     * Параметры аннотации необязательны!
     *
     * @param string $unserialized
     *
     * @return JsonResponse $content
     * @QueryParams(
            var="unserialized",
            class="Prokl\AnnotatedParamResolverBundle\Examples\RequestBodyConverted",
            validate=true
    )
     */
    public function action2(
        string $unserialized
    ): JsonResponse {
        return new JsonResponse();
    }

    /**
     * Параметры аннотации необязательны!
     *
     * @param string $unserialized
     *
     * @return JsonResponse $content
     * @RequestBody(
    var="unserialized",
    class="Prokl\AnnotatedParamResolverBundle\Examples\RequestBodyConverted",
    validate=true
    )
     */
    public function action3(
        string $unserialized
    ): JsonResponse {
        return new JsonResponse();
    }
}
