<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function createMockedRequest(array $headers = [], array $queryParameters = [], array $attributes = []): Request
    {
        $request = $this->createMock(Request::class);

        $headerBag = $this->createMock(HeaderBag::class);
        $headerBag->method('has')->willReturnCallback(function ($requested) use ($headers) {
            return array_key_exists($requested, $headers);
        });
        $headerBag->method('get')->willReturnCallback(function ($requested) use ($headers) {
            return array_key_exists($requested, $headers) ? $headers[$requested] : null;
        });
        $request->headers = $headerBag;

        $queryParametersBag = $this->createMock(ParameterBag::class);
        $queryParametersBag->method('has')->willReturnCallback(function ($requested) use ($queryParameters) {
            return array_key_exists($requested, $queryParameters);
        });
        $queryParametersBag->method('get')->willReturnCallback(function ($requested) use ($queryParameters) {
            return array_key_exists($requested, $queryParameters) ? $queryParameters[$requested] : null;
        });
        $request->query = $queryParametersBag;

        $attributesParametersBag = $this->createMock(ParameterBag::class);
        $attributesParametersBag->method('has')->willReturnCallback(function ($requested) use ($attributes) {
            return array_key_exists($requested, $attributes);
        });
        $attributesParametersBag->method('get')->willReturnCallback(function ($requested) use ($attributes) {
            return array_key_exists($requested, $attributes) ? $attributes[$requested] : null;
        });
        $request->attributes = $attributesParametersBag;

        return $request;
    }
}
