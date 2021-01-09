<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\Creation;

use PoolTournament\Domain\Module\Match\Creation\DTO\Request as RequestDTO;
use PoolTournament\Domain\Module\Match\Creation\DTO\Response as ResponseDTO;
use PoolTournament\Domain\Module\Match\Creation\Exception\LooserInfoUpdateErrorException;
use PoolTournament\Domain\Module\Match\Creation\Exception\MatchCreationErrorException;
use PoolTournament\Domain\Module\Match\Creation\Exception\WinnerInfoUpdateErrorException;
use PoolTournament\Domain\Module\Match\Entity\FriendEntity;

class Service
{
    private const WINNER_POINTS = 3;
    private const LOOSER_POINTS = 1;

    private MatchRepository $matchRepository;
    private FriendRepository $friendRepository;

    public function __construct(MatchRepository $matchRepository, FriendRepository $friendRepository)
    {
        $this->matchRepository = $matchRepository;
        $this->friendRepository = $friendRepository;
    }

    /**
     * @param RequestDTO $request
     *
     * @throws LooserInfoUpdateErrorException
     * @throws MatchCreationErrorException
     * @throws WinnerInfoUpdateErrorException
     *
     * @return ResponseDTO
     */
    public function create(RequestDTO $request): ResponseDTO
    {
        $createdMatch = $this->matchRepository->create($request);
        if (empty($createdMatch)) {
            throw new MatchCreationErrorException();
        }

        $updatedWinnerFriend = $this->updateWinnerFriend($request);
        $createdMatch->setWinner($updatedWinnerFriend);

        if (!$request->isMatchAbsence()) {
            $updatedLooserFriend = $this->updateLooserFriend($request);
            $createdMatch->setLooser($updatedLooserFriend);
        }

        return new ResponseDTO($createdMatch);
    }

    /**
     * @param RequestDTO $request
     *
     * @throws WinnerInfoUpdateErrorException
     *
     * @return FriendEntity
     */
    public function updateWinnerFriend(RequestDTO $request): FriendEntity
    {
        $updatedWinnerFriend = $this->friendRepository->updateWinnerInfo(
            $request->getWinnerId(),
            self::WINNER_POINTS
        );

        if (empty($updatedWinnerFriend)) {
            throw new WinnerInfoUpdateErrorException();
        }

        return $updatedWinnerFriend;
    }

    /**
     * @param RequestDTO $request
     *
     * @throws LooserInfoUpdateErrorException
     *
     * @return FriendEntity
     */
    public function updateLooserFriend(RequestDTO $request): FriendEntity
    {
        $updatedLooserFriend = $this->friendRepository->updateLooserInfo(
            $request->getLooserId(),
            self::LOOSER_POINTS,
            $request->getLooserBallsLeft()
        );

        if (empty($updatedLooserFriend)) {
            throw new LooserInfoUpdateErrorException();
        }
        return $updatedLooserFriend;
    }
}
