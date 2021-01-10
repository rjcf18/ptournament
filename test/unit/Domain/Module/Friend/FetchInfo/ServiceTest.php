<?php declare(strict_types=1);
namespace UnitTest\Domain\Module\Friend\FetchInfo;

use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PoolTournament\Domain\Module\Friend\Entity\Friend;
use PoolTournament\Domain\Module\Friend\FetchInfo\DTO\Request;
use PoolTournament\Domain\Module\Friend\FetchInfo\DTO\Response as ResponseDTO;
use PoolTournament\Domain\Module\Friend\FetchInfo\Exception\FriendNotFoundException;
use PoolTournament\Domain\Module\Friend\FetchInfo\FriendRepository;
use PoolTournament\Domain\Module\Friend\FetchInfo\Service;

class ServiceTest extends TestCase
{
    private MockObject|FriendRepository $friendRepositoryMock;

    private Service $service;

    protected function setUp(): void
    {
        $this->friendRepositoryMock = $this->getFriendRepositoryMock();
        $this->service = new Service($this->friendRepositoryMock);
    }

    /**
     * @throws FriendNotFoundException
     */
    public function testFetchInfoWhenFriendNotFoundThrowsException()
    {
        $this->friendRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->willReturn(null);

        $this->expectException(FriendNotFoundException::class);
        $this->expectExceptionMessage(FriendNotFoundException::MESSAGE);

        $this->service->fetchInfo(new Request(1));
    }

    /**
     * @throws FriendNotFoundException
     */
    public function testFetchInfoWhenFriendIsFoundReturnsFriend()
    {
        $friend = new Friend(1, 'Test', 0, 0, new DateTimeImmutable(), new DateTimeImmutable());

        $this->friendRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->willReturn($friend);

        $responseDTO = $this->service->fetchInfo(new Request(1));

        $this->assertInstanceOf(ResponseDTO::class, $responseDTO);
        $this->assertEquals($friend, $responseDTO->getFriend());
    }

    private function getFriendRepositoryMock(): MockObject|FriendRepository
    {
        return $this->getMockBuilder(FriendRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getById'])
            ->getMock();
    }
}