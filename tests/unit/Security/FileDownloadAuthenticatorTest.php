<?php

namespace App\Tests\unit\Security;

use App\Entity\Book\ElectronicBook\File;
use App\Entity\Mediatheque\FileDownloadToken;
use App\Entity\User;
use App\Security\FileDownloadAuthenticator;
use App\Tests\TestCase;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class FileDownloadAuthenticatorTest extends TestCase
{
    private MockClock $clock;

    private FileDownloadAuthenticator $fileDownloadAuthenticator;
    private EntityManagerInterface&MockObject $mockedEntityManager;
    private LoggerInterface&MockObject $mockedLogger;

    protected function setUp(): void
    {
        $this->mockedEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockedLogger = $this->createMock(LoggerInterface::class);

        $this->clock = new MockClock('2026-01-01 12:00:00');

        $this->fileDownloadAuthenticator = new FileDownloadAuthenticator(
            $this->mockedEntityManager,
            $this->mockedLogger,
            $this->clock
        );
    }

    #[DataProvider('supportDataProvider')]
    public function testSupport(Request $request, bool $support)
    {
        $this->assertEquals(
            $support,
            $this->fileDownloadAuthenticator->supports(
                $request
            )
        );
    }

    public static function supportDataProvider(): array
    {
        return [
            'Support' => [
                'request' => new Request(query: ['t' => '123456879']),
                'support' => true
            ],
            "Don't support" => [
                'request' => new Request(query: ['token' => '123456879']),
                'support' => false
            ]
        ];
    }

    #[DataProvider('authenticateDataProvider')]
    public function testAuthenticate(
        Request     $request,
        ?Closure    $fileDownloadTokenBuilder,
        ?int        $tokenAge,
        ?string     $expectedExceptionClass,
        ?string     $expectedExceptionMessage
    ) {
        if (is_string($expectedExceptionClass)) {
            $this->expectException($expectedExceptionClass);
        }
        if (is_string($expectedExceptionMessage)) {
            $this->expectExceptionMessageIsOrContains($expectedExceptionMessage);
        }

        $fileDownloadToken = is_callable($fileDownloadTokenBuilder) ? $fileDownloadTokenBuilder($this) : null;

        if ($fileDownloadToken instanceof MockObject) {
            $tokenCreationDate = is_int($tokenAge) ?
                \DateTime::createFromFormat('U', $this->clock->now()->getTimestamp())
                ->sub(new \DateInterval(sprintf('PT%dS', $tokenAge))) :
                null;
            $fileDownloadToken->method('getCreated')->willReturn($tokenCreationDate);
        }

        $tokenRepository = $this->createMock(EntityRepository::class);
        $tokenRepository->method('findOneBy')->willReturn($fileDownloadToken);
        $this->mockedEntityManager->method('getRepository')->willReturn($tokenRepository);

        $this->assertInstanceOf(
            SelfValidatingPassport::class,
            $this->fileDownloadAuthenticator->authenticate(
                $request
            )
        );
    }

    public static function authenticateDataProvider()
    {
        yield "There is no token class for the given route" => [
            'request' => new Request(
                query: ['t' => '123456'],
                attributes: [
                    '_route' => 'some_route',
                    '_api_resource_class' => '\SomeClass'
                ]
            ),
            'fileDownloadTokenBuilder' => null,
            'tokenAge' => null,
            'expectedExceptionClass' => \LogicException::class,
            'expectedExceptionMessage' => "No token class known for route some_route"
        ];

        yield "There is no token matching the given code" => [
            'request' => new Request(
                query: ['t' => '123456'],
                attributes: [
                    '_route' => 'api_book_files_get_item',
                    '_api_resource_class' => File::class
                ]
            ),
            'fileDownloadTokenBuilder' => null,
            'tokenAge' => null,
            'expectedExceptionClass' => AuthenticationException::class,
            'expectedExceptionMessage' => "Invalid token"
        ];

        yield "The token is too old" => [
            'request' => new Request(
                query: ['t' => '123456'],
                attributes: [
                    '_route' => 'api_book_files_get_item',
                    '_api_resource_class' => File::class
                ]
            ),
            'fileDownloadTokenBuilder' => static function (self $test) {
                return $test->createMockedFileDownloadToken('abcdefg');
            },
            'tokenAge' => 601,
            'expectedExceptionClass' => AuthenticationException::class,
            'expectedExceptionMessage' => "Expired token"
        ];

        yield "Everything is fine" => [
            'request' => new Request(
                query: ['t' => '123456'],
                attributes: [
                    '_route' => 'api_book_files_get_item',
                    '_api_resource_class' => File::class
                ]
            ),
            'fileDownloadTokenBuilder' => static function (self $test) {
                return $test->createMockedFileDownloadToken('abcdefg');
            },
            'tokenAge' => 59,
            'expectedExceptionClass' => null,
            'expectedExceptionMessage' => null
        ];
    }

    private function createMockedFileDownloadToken(string $userSub): FileDownloadToken
    {
        $user = $this->createMock(User::class);
        $user->method('getSub')->willReturn($userSub);

        $token = $this->createMock(FileDownloadToken::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }
}
