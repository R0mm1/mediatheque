<?php

namespace App\Tests\unit\Security;

use App\Security\Exception\ExpiredTokenException;
use App\Security\Exception\InvalidTokenException;
use App\Security\OAuth2Authenticator;
use App\Service\TokenDecoderInterface;
use App\Tests\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\ExpiredException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class OAuth2AuthenticatorTest extends TestCase
{
    const ISSUER = 'https://auth.myDomain.com';
    private OAuth2Authenticator $OAuth2Authenticator;
    private TokenDecoderInterface $mockedTokenDecoder;
    private EntityManagerInterface $mockedEntityManager;
    private LoggerInterface $mockedLogger;

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

    /**
     * @dataProvider supportDataProvider
     */
    public function testSupport(Request $request, bool $support)
    {
        $this->assertEquals(
            $support,
            $this->OAuth2Authenticator->supports($request)
        );
    }

    public function supportDataProvider(): \Generator
    {
        yield "The request has no Authorization header" => [
            'request' => $this->createMockedRequest([]),
            'support' => false
        ];

        yield "The request has an Authorization header" => [
            'request' => $this->createMockedRequest([
                'Authorization' => 'abc'
            ]),
            'support' => true
        ];
    }

    /**
     * @dataProvider authenticateDataProvider
     */
    public function testAuthenticate(
        Request   $request,
        ?string   $tokenDecoderExceptionClass,
        \stdClass $jwtPayload,
        string    $expectedLoggingMethod,
        string    $expectedLoggingMessage,
        array     $expectedLoggingContext,
        ?string   $expectedExceptionClass,
        ?string   $expectedExceptionMessage
    )
    {
        if (is_string($tokenDecoderExceptionClass)) {
            $this->mockedTokenDecoder->method('decode')->willThrowException(
                new $tokenDecoderExceptionClass("Token decoder error")
            );
        }
        $this->mockedTokenDecoder->method('decode')->willReturn($jwtPayload);

        $this->mockedLogger
            ->expects($this->once())
            ->method($expectedLoggingMethod)
            ->with($expectedLoggingMessage, $expectedLoggingContext);

        if (is_string($expectedExceptionClass)) {
            $this->expectException($expectedExceptionClass);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $passport = $this->OAuth2Authenticator->authenticate($request);
        $this->assertInstanceOf(SelfValidatingPassport::class, $passport);
    }

    public function authenticateDataProvider(): \Generator
    {
        //Theoretically this case is not possible because of the "support" method
        yield "The request has no Authorization header" => [
            'request' => $this->createMockedRequest([]),
            'tokenDecoderExceptionClass' => null,
            'jwtPayload' => new \stdClass(),
            'expectedLoggingMethod' => 'alert',
            'expectedLoggingMessage' => 'Attempt to login with a missing or badly formatted token',
            'expectedLoggingContext' => [
                'Authorization' => null
            ],
            'expectedExceptionClass' => InvalidTokenException::class,
            'expectedExceptionMessage' => "Invalid token"
        ];

        yield "The request has a badly formatted Authorization header" => [
            'request' => $this->createMockedRequest([
                'Authorization' => '123456789'
            ]),
            'tokenDecoderExceptionClass' => null,
            'jwtPayload' => new \stdClass(),
            'expectedLoggingMethod' => 'alert',
            'expectedLoggingMessage' => 'Attempt to login with a missing or badly formatted token',
            'expectedLoggingContext' => [
                'Authorization' => '123456789'
            ],
            'expectedExceptionClass' => InvalidTokenException::class,
            'expectedExceptionMessage' => "Invalid token"
        ];

        yield "The token can't be decoded because it's expired" => [
            'request' => $this->createMockedRequest([
                'Authorization' => 'Bearer 123456789'
            ]),
            'tokenDecoderExceptionClass' => ExpiredException::class,
            'jwtPayload' => new \stdClass(),
            'expectedLoggingMethod' => 'info',
            'expectedLoggingMessage' => "Attempt to login with an expired token",
            'expectedLoggingContext' => [
                'Authorization' => 'Bearer 123456789'
            ],
            'expectedExceptionClass' => ExpiredTokenException::class,
            'expectedExceptionMessage' => "Token expire"
        ];

        yield "The token can't be decoded for any other reason" => [
            'request' => $this->createMockedRequest([
                'Authorization' => 'Bearer 123456789'
            ]),
            'tokenDecoderExceptionClass' => \Exception::class,
            'jwtPayload' => new \stdClass(),
            'expectedLoggingMethod' => 'alert',
            'expectedLoggingMessage' => "Authentication failed: Token decoder error",
            'expectedLoggingContext' => [
                'Authorization' => 'Bearer 123456789'
            ],
            'expectedExceptionClass' => InvalidTokenException::class,
            'expectedExceptionMessage' => "Invalid token"
        ];

        $jwtPayload = $this->createJwtPayload([
            'sub' => '123'
        ]);
        yield "The token is missing an iss property" => [
            'request' => $this->createMockedRequest([
                'Authorization' => 'Bearer 123456789'
            ]),
            'tokenDecoderExceptionClass' => null,
            'jwtPayload' => $jwtPayload,
            'expectedLoggingMethod' => 'alert',
            'expectedLoggingMessage' => "Attempt to log in using a token with an invalid issuer",
            'expectedLoggingContext' => [
                'Authorization' => 'Bearer 123456789',
                'JwtPayload' => $jwtPayload
            ],
            'expectedExceptionClass' => InvalidTokenException::class,
            'expectedExceptionMessage' => "Invalid token"
        ];

        $jwtPayload = $this->createJwtPayload([
            'sub' => '123',
            'iss' => 'https://i-am-not-the-good-issuer.com'
        ]);
        yield "The token has an invalid iss property" => [
            'request' => $this->createMockedRequest([
                'Authorization' => 'Bearer 123456789'
            ]),
            'tokenDecoderExceptionClass' => null,
            'jwtPayload' => $this->createJwtPayload([
                'sub' => '123',
                'iss' => 'https://i-am-not-the-good-issuer.com'
            ]),
            'expectedLoggingMethod' => 'alert',
            'expectedLoggingMessage' => "Attempt to log in using a token with an invalid issuer",
            'expectedLoggingContext' => [
                'Authorization' => 'Bearer 123456789',
                'JwtPayload' => $jwtPayload
            ],
            'expectedExceptionClass' => InvalidTokenException::class,
            'expectedExceptionMessage' => "Invalid token"
        ];

        yield "Everything is fine" => [
            'request' => $this->createMockedRequest([
                'Authorization' => 'Bearer 123456789'
            ]),
            'tokenDecoderExceptionClass' => null,
            'jwtPayload' => $this->createJwtPayload([
                'sub' => '123',
                'iss' => self::ISSUER
            ]),
            'expectedLoggingMethod' => 'info',
            'expectedLoggingMessage' => "User with sub 123 successfully logged in",
            'expectedLoggingContext' => [],
            'expectedExceptionClass' => null,
            'expectedExceptionMessage' => null
        ];
    }

    private function createJwtPayload(array $data): \stdClass
    {
        $jwtPayload = new \stdClass();
        foreach ($data as $key => $value) {
            $jwtPayload->$key = $value;
        }
        return $jwtPayload;
    }
}
