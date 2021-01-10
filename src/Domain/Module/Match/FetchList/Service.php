<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\FetchList;

use PoolTournament\Domain\Module\Match\FetchList\DTO\Request as RequestDTO;
use PoolTournament\Domain\Module\Match\FetchList\DTO\Response as ResponseDTO;

class Service
{
    private MatchRepository $matchRepository;

    public function __construct(MatchRepository $matchRepository)
    {
        $this->matchRepository = $matchRepository;
    }

    public function fetchList(RequestDTO $request): ResponseDTO
    {
        $matchCollection = $request->getFriendId()
            ? $this->matchRepository->fetchListForFriend($request->getFriendId())
            : $this->matchRepository->fetchAll();

        return new ResponseDTO($matchCollection);
    }
}
