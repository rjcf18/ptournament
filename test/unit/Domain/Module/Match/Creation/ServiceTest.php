<?php declare(strict_types=1);
namespace UnitTest\Domain\Module\Match\Creation;

use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PoolTournament\Domain\Module\Match\Creation\DTO\Request;
use PoolTournament\Domain\Module\Match\Creation\DTO\Response as ResponseDTO;
use PoolTournament\Domain\Module\Match\Creation\Exception\FriendsAlreadyPlayedException;
use PoolTournament\Domain\Module\Match\Creation\Exception\LooserInfoUpdateErrorException;
use PoolTournament\Domain\Module\Match\Creation\Exception\MatchCreationErrorException;
use PoolTournament\Domain\Module\Match\Creation\Exception\WinnerInfoUpdateErrorException;
use PoolTournament\Domain\Module\Match\Creation\FriendRepository;
use PoolTournament\Domain\Module\Match\Creation\MatchRepository;
use PoolTournament\Domain\Module\Match\Creation\Service;
use PoolTournament\Domain\Module\Match\Entity\FriendEntity;
use PoolTournament\Domain\Module\Match\Entity\MatchEntity;

class ServiceTest extends TestCase
{
    private MockObject|MatchRepository $matchRepositoryMock;
    private MockObject|FriendRepository $friendRepositoryMock;
    private Service $service;

    protected function setUp(): void
    {
        $this->matchRepositoryMock = $this->getMatchRepositoryMock();
        $this->friendRepositoryMock = $this->getFriendRepositoryMock();
        $this->service = new Service($this->matchRepositoryMock, $this->friendRepositoryMock);
    }

    /**
     * @throws FriendsAlreadyPlayedException
     * @throws LooserInfoUpdateErrorException
     * @throws MatchCreationErrorException
     * @throws WinnerInfoUpdateErrorException
     */
    public function testCreateWhenPlayersAlreadyPlayedThrowsException()
    {
        $this->matchRepositoryMock
            ->expects($this->once())
            ->method('friendsAlreadyPlayed')
            ->willReturn(true);

        $this->expectException(FriendsAlreadyPlayedException::class);
        $this->expectExceptionMessage(FriendsAlreadyPlayedException::MESSAGE);

        $this->service->create(
            new Request(1, 2, 1, false, new DateTimeImmutable())
        );
    }

    /**
     * @throws FriendsAlreadyPlayedException
     * @throws LooserInfoUpdateErrorException
     * @throws MatchCreationErrorException
     * @throws WinnerInfoUpdateErrorException
     */
    public function testCreateWhenErrorOccursWhileSavingMatchInfoThrowsException()
    {
        $this->matchRepositoryMock
            ->expects($this->once())
            ->method('friendsAlreadyPlayed')
            ->willReturn(false);

        $this->matchRepositoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn(null);

        $this->expectException(MatchCreationErrorException::class);
        $this->expectExceptionMessage(MatchCreationErrorException::MESSAGE);

        $this->service->create(
            new Request(1, 2, 1, false, new DateTimeImmutable())
        );
    }

    /**
     * @throws FriendsAlreadyPlayedException
     * @throws LooserInfoUpdateErrorException
     * @throws MatchCreationErrorException
     * @throws WinnerInfoUpdateErrorException
     */
    public function testCreateWhenErrorOccursWhileSavingWinnerInfoThrowsException()
    {
        $expectedMatchEntity = new MatchEntity(
            1,
            new FriendEntity(1, 'Friend1', 0, 0, new DateTimeImmutable(), new DateTimeImmutable()),
            new FriendEntity(2, 'Friend2', 0, 0, new DateTimeImmutable(), new DateTimeImmutable()),
            3,
            false,
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->matchRepositoryMock
            ->expects($this->once())
            ->method('friendsAlreadyPlayed')
            ->willReturn(false);

        $this->matchRepositoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($expectedMatchEntity);

        $this->friendRepositoryMock
            ->expects($this->once())
            ->method('updateWinnerInfo')
            ->willReturn(null);

        $this->expectException(WinnerInfoUpdateErrorException::class);
        $this->expectExceptionMessage(WinnerInfoUpdateErrorException::MESSAGE);

        $this->service->create(
            new Request(1, 2, 1, false, new DateTimeImmutable())
        );
    }

    /**
     * @throws FriendsAlreadyPlayedException
     * @throws LooserInfoUpdateErrorException
     * @throws MatchCreationErrorException
     * @throws WinnerInfoUpdateErrorException
     */
    public function testCreateWhenErrorOccursWhileSavingLooserInfoThrowsException()
    {
        $expectedWinner = new FriendEntity(1, 'Friend1', 0, 0, new DateTimeImmutable(), new DateTimeImmutable());
        $expectedLooser = new FriendEntity(1, 'Friend1', 0, 0, new DateTimeImmutable(), new DateTimeImmutable());

        $expectedMatchEntity = new MatchEntity(
            1,
            $expectedWinner,
            $expectedLooser,
            3,
            false,
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->matchRepositoryMock
            ->expects($this->once())
            ->method('friendsAlreadyPlayed')
            ->willReturn(false);

        $this->matchRepositoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($expectedMatchEntity);

        $this->friendRepositoryMock
            ->expects($this->once())
            ->method('updateWinnerInfo')
            ->willReturn($expectedWinner);

        $this->friendRepositoryMock
            ->expects($this->once())
            ->method('updateLooserInfo')
            ->willReturn(null);

        $this->expectException(LooserInfoUpdateErrorException::class);
        $this->expectExceptionMessage(LooserInfoUpdateErrorException::MESSAGE);

        $this->service->create(
            new Request(1, 2, 1, false, new DateTimeImmutable())
        );
    }

    /**
     * @throws FriendsAlreadyPlayedException
     * @throws LooserInfoUpdateErrorException
     * @throws MatchCreationErrorException
     * @throws WinnerInfoUpdateErrorException
     */
    public function testCreateWhenNoErrorsOccurReturnsMatchWithUpdatedInfo()
    {
        $expectedWinner = new FriendEntity(1, 'Friend1', 0, 0, new DateTimeImmutable(), new DateTimeImmutable());
        $expectedLooser = new FriendEntity(1, 'Friend1', 0, 0, new DateTimeImmutable(), new DateTimeImmutable());

        $expectedMatchEntity = new MatchEntity(
            1,
            $expectedWinner,
            $expectedLooser,
            3,
            false,
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->matchRepositoryMock
            ->expects($this->once())
            ->method('friendsAlreadyPlayed')
            ->willReturn(false);

        $this->matchRepositoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($expectedMatchEntity);

        $this->friendRepositoryMock
            ->expects($this->once())
            ->method('updateWinnerInfo')
            ->willReturn($expectedWinner);

        $this->friendRepositoryMock
            ->expects($this->once())
            ->method('updateLooserInfo')
            ->willReturn($expectedLooser);

        $creationResponse = $this->service->create(
            new Request(1, 2, 1, false, new DateTimeImmutable())
        );

        $this->assertInstanceOf(ResponseDTO::class, $creationResponse);
        $this->assertEquals($expectedMatchEntity, $creationResponse->getMatch());
    }

    private function getMatchRepositoryMock(): MockObject|MatchRepository
    {
        return $this->getMockBuilder(MatchRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create', 'friendsAlreadyPlayed'])
            ->getMock();
    }

    private function getFriendRepositoryMock(): MockObject|FriendRepository
    {
        return $this->getMockBuilder(FriendRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['updateWinnerInfo', 'updateLooserInfo'])
            ->getMock();
    }
}