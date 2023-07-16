<?php

namespace App\Normalizer;

use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class MeilisearchNormalizer implements NormalizerInterface, EncoderInterface, CacheableSupportsMethodInterface
{
    const FORMAT = 'meilisearch';

    public function __construct(
        private readonly ObjectNormalizer $objectNormalizer,
    )
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return array_map(
            fn(object $item) => [
                'type' => $item::class,
                ...$this->objectNormalizer->normalize($item, 'json', $context)
            ],
            $object
        );
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return is_array($data) && $format === self::FORMAT;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    public function encode(mixed $data, string $format, array $context = []): string
    {
        return json_encode($data);
    }

    public function supportsEncoding(string $format): bool
    {
        return $format === self::FORMAT;
    }
}
