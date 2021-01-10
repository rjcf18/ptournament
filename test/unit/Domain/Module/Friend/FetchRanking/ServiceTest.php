<?php declare(strict_types=1);
namespace UnitTest\Domain\Module\Friend\FetchRanking;

use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PoolTournament\Domain\Module\Friend\Entity\Friend;
use PoolTournament\Domain\Module\Friend\FetchRanking\FriendCollection;
use PoolTournament\Domain\Module\Friend\FetchRanking\ResponseDTO;
use PoolTournament\Domain\Module\Friend\FetchRanking\FriendRepository;
use PoolTournament\Domain\Module\Friend\FetchRanking\Service;

class ServiceTest extends TestCase
{
    private MockObject|FriendRepository $friendRepositoryMock;

    private Service $service;

    protected function setUp(): void
    {
        $this->friendRepositoryMock = $this->getFriendRepositoryMock();
        $this->service = new Service($this->friendRepositoryMock);
    }

    public function testFetchRankingWhenFriendsAreFoundReturnsCollection()
    {
        $friendCollection = FriendCollection::create();
        $friendCollection->addFriend(
            new Friend(1, 'Test', 0, 0, new DateTimeImmutable(), new DateTimeImmutable())
        );

        $this->friendRepositoryMock
            ->expects($this->once())
            ->method('fetchRanking')
            ->willReturn($friendCollection);

        $responseDTO = $this->service->fetchRanking();

        $this->assertInstanceOf(ResponseDTO::class, $responseDTO);
        $this->assertEquals($friendCollection, $responseDTO->getFriendCollection());
    }

    private function getFriendRepositoryMock(): MockObject|FriendRepository
    {
        return $this->getMockBuilder(FriendRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetchRanking'])
            ->getMock();
    }
}