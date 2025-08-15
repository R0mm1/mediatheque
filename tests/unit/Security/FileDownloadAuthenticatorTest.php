<?php

namespace App\Tests\unit\Security;

use App\Entity\Book\ElectronicBook\File;
use App\Entity\Mediatheque\FileDownloadToken;
use App\Entity\User;
use App\Security\FileDownloadAuthenticator;
use App\Tests\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

#[\PHPUnit\Framework\Attributes\Group('time-sensitive')]
class FileDownloadAuthenticatorTest extends TestCase
{
    private FileDownloadAuthenticator $fileDownloadAuthenticator;
    private EntityManagerInterface $mockedEntityManager;
    private LoggerInterface $mockedLogger;

    protected function setUp(): void
    {
        $this->mockedEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockedLogger = $this->createMock(LoggerInterface::class);

        $this->fileDownloadAuthenticator = new FileDownloadAuthenticator(
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
            $this->fileDownloadAuthenticator->supports(
                $request
            )
        );
    }

    public function supportDataProvider(): array
    {
        return [
            'Support' => [
                'request' => $this->createMockedRequest([], ['t' => '123456879']),
                'support' => true
            ],
            "Don't support" => [
                'request' => $this->createMockedRequest([], ['token' => '123456879']),
                'support' => false
            ]
        ];
    }

    /**
     * @dataProvider authenticateDataProvider
     */
    public function testAuthenticate(
        Request     $request,
        ?MockObject $fileDownloadToken,
        ?int        $tokenAge,
        ?string     $expectedExceptionClass,
        ?string     $expectedExceptionMessage
    )
    {
        if (is_string($expectedExceptionClass)) {
            $this->expectException($expectedExceptionClass);
        }
        if (is_string($expectedExceptionMessage)) {
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        if ($fileDownloadToken instanceof MockObject) {
            $tokenCreationDate = is_int($tokenAge) ?
                \DateTime::createFromFormat('U', ClockMock::time())
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

    public function authenticateDataProvider()
    {
        yield "There is no token class for the given route" => [
            'request' => $this->createMockedRequest(
                [],
                ['t' => '123456'],
                [
                    '_route' => 'some_route',
                    '_api_resource_class' => '\SomeClass'
                ]
            ),
            'fileDownloadToken' => null,
            'tokenAge' => null,
            'expectedExceptionClass' => \LogicException::class,
            'expectedExceptionMessage' => "No token class known for route some_route"
        ];

        yield "There is no token matching the given code" => [
            'request' => $this->createMockedRequest(
                [],
                ['t' => '123456'],
                [
                    '_route' => 'api_book_files_get_item',
                    '_api_resource_class' => File::class
                ]
            ),
            'fileDownloadToken' => null,
            'tokenAge' => null,
            'expectedExceptionClass' => AuthenticationException::class,
            'expectedExceptionMessage' => "Invalid token"
        ];

        yield "The token is too old" => [
            'request' => $this->createMockedRequest(
                [],
                ['t' => '123456'],
                [
                    '_route' => 'api_book_files_get_item',
                    '_api_resource_class' => File::class
                ]
            ),
            'fileDownloadToken' => $this->createMockedFileDownloadToken('abcdefg'),
            'tokenAge' => 601,
            'expectedExceptionClass' => AuthenticationException::class,
            'expectedExceptionMessage' => "Expired token"
        ];

        yield "Everything is fine" => [
            'request' => $this->createMockedRequest(
                [],
                ['t' => '123456'],
                [
                    '_route' => 'api_book_files_get_item',
                    '_api_resource_class' => File::class
                ]
            ),
            'fileDownloadToken' => $this->createMockedFileDownloadToken('abcdefg'),
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
