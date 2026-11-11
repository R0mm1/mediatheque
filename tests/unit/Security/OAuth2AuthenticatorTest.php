<?php

namespace App\Tests\unit\Security;

use App\Security\Exception\ExpiredTokenException;
use App\Security\Exception\InvalidTokenException;
use App\Security\OAuth2Authenticator;
use App\Service\TokenDecoderInterface;
use App\Tests\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\ExpiredException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class OAuth2AuthenticatorTest extends TestCase
{
    const ISSUER = 'https://auth.myDomain.com';
    private OAuth2Authenticator $OAuth2Authenticator;
    private TokenDecoderInterface&MockObject $mockedTokenDecoder;
    private EntityManagerInterface&MockObject $mockedEntityManager;
    private LoggerInterface&MockObject $mockedLogger;

    protected function setUp(): void
    {
        $this->mockedTokenDecoder = $this->createMock(TokenDecoderInterface::class);
        $this->mockedEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockedLogger = $this->createMock(LoggerInterface::class);

        $this->OAuth2Authenticator = new OAuth2Authenticator(
            self::ISSUER,
            $this->mockedTokenDecoder,
            $this->mockedEntityManager,
            $this->mockedLogger
        );
    }

    #[DataProvider('supportDataProvider')]
    public function testSupport(Request $request, bool $support)
    {
        $this->assertEquals(
            $support,
            $this->OAuth2Authenticator->supports($request)
        );
    }

    public static function supportDataProvider(): \Generator
    {
        yield "The request has no Authorization header" => [
            'request' => new Request(),
            'support' => false
        ];

        yield "The request has an Authorization header" => [
            'request' => new Request(server: [
                'HTTP_AUTHORIZATION' => 'Bearer 123456789'
            ]),
            'support' => true
        ];
    }

    #[DataProvider('authenticateDataProvider')]
    public function testAuthenticate(
        Request $request,
        ?string   $tokenDecoderExceptionClass,
        \stdClass $jwtPayload,
        ?string   $expectedExceptionClass,
        ?string   $expectedExceptionMessage
    ) {
        if (is_string($tokenDecoderExceptionClass)) {
            $this->mockedTokenDecoder->method('decode')->willThrowException(
                new $tokenDecoderExceptionClass("Token decoder error")
            );
        }
        $this->mockedTokenDecoder->method('decode')->willReturn($jwtPayload);

        if (is_string($expectedExceptionClass)) {
            $this->expectException($expectedExceptionClass);
            $this->expectExceptionMessageIsOrContains($expectedExceptionMessage);
        }

        $passport = $this->OAuth2Authenticator->authenticate($request);
        $this->assertInstanceOf(SelfValidatingPassport::class, $passport);
    }

    public static function authenticateDataProvider(): \Generator
    {
        //Theoretically this case is not possible because of the "support" method
        yield "The request has no Authorization header" => [
            'request' => new Request(),
            'tokenDecoderExceptionClass' => null,
            'jwtPayload' => new \stdClass(),
            'expectedExceptionClass' => InvalidTokenException::class,
            'expectedExceptionMessage' => "Invalid token"
        ];

        yield "The request has a badly formatted Authorization header" => [
            'request' => new Request(server: [
                'HTTP_AUTHORIZATION' => '123456789'
            ]),
            'tokenDecoderExceptionClass' => null,
            'jwtPayload' => new \stdClass(),
            'expectedExceptionClass' => InvalidTokenException::class,
            'expectedExceptionMessage' => "Invalid token"
        ];

        yield "The token can't be decoded because it's expired" => [
            'request' => new Request(server: [
                'HTTP_AUTHORIZATION' => 'Bearer 123456789'
            ]),
            'tokenDecoderExceptionClass' => ExpiredException::class,
            'jwtPayload' => new \stdClass(),
            'expectedExceptionClass' => ExpiredTokenException::class,
            'expectedExceptionMessage' => "Token expire"
        ];

        yield "The token can't be decoded for any other reason" => [
            'request' => new Request(server: [
                'HTTP_AUTHORIZATION' => 'Bearer 123456789'
            ]),
            'tokenDecoderExceptionClass' => \Exception::class,
            'jwtPayload' => new \stdClass(),
            'expectedExceptionClass' => InvalidTokenException::class,
            'expectedExceptionMessage' => "Invalid token"
        ];

        $jwtPayload = self::createJwtPayload([
            'sub' => '123'
        ]);
        yield "The token is missing an iss property" => [
            'request' => new Request(server: [
                'HTTP_AUTHORIZATION' => 'Bearer 123456789'
            ]),
            'tokenDecoderExceptionClass' => null,
            'jwtPayload' => $jwtPayload,
            'expectedExceptionClass' => InvalidTokenException::class,
            'expectedExceptionMessage' => "Invalid token"
        ];

        $jwtPayload = self::createJwtPayload([
            'sub' => '123',
            'iss' => 'https://i-am-not-the-good-issuer.com'
        ]);
        yield "The token has an invalid iss property" => [
            'request' => new Request(server: [
                'HTTP_AUTHORIZATION' => 'Bearer 123456789'
            ]),
            'tokenDecoderExceptionClass' => null,
            'jwtPayload' => self::createJwtPayload([
                'sub' => '123',
                'iss' => 'https://i-am-not-the-good-issuer.com'
            ]),
            'expectedExceptionClass' => InvalidTokenException::class,
            'expectedExceptionMessage' => "Invalid token"
        ];

        yield "Everything is fine" => [
            'request' => new Request(server: [
                'HTTP_AUTHORIZATION' => 'Bearer 123456789'
            ]),
            'tokenDecoderExceptionClass' => null,
            'jwtPayload' => self::createJwtPayload([
                'sub' => '123',
                'iss' => self::ISSUER
            ]),
            'expectedExceptionClass' => null,
            'expectedExceptionMessage' => null
        ];
    }

    private static function createJwtPayload(array $data): \stdClass
    {
        $jwtPayload = new \stdClass();
        foreach ($data as $key => $value) {
            $jwtPayload->$key = $value;
        }
        return $jwtPayload;
    }
}
