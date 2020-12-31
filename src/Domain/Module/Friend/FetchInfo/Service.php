<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Friend\FetchInfo;

use PoolTournament\Domain\Module\Friend\FetchInfo\DTO\Request as RequestDTO;
use PoolTournament\Domain\Module\Friend\FetchInfo\DTO\Response as ResponseDTO;
use PoolTournament\Domain\Module\Friend\FetchInfo\Exception\FriendNotFoundException;

class Service
{
    private FriendRepository $friendRepository;

    public function __construct(FriendRepository $friendRepository)
    {
        $this->friendRepository = $friendRepository;
    }

    /**
     * @param RequestDTO $request
     *
     * @throws FriendNotFoundException
     *
     * @return ResponseDTO
     */
    public function fetchInfo(RequestDTO $request): ResponseDTO
    {
        $friend = $this->friendRepository->getById($request->getFriendId());

        if (empty($friend)) {
            throw new FriendNotFoundException();
        }

        return new ResponseDTO($friend);
    }
}