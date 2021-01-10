<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Friend\FetchRanking;

class Service
{
    private FriendRepository $friendRepository;

    public function __construct(FriendRepository $friendRepository)
    {
        $this->friendRepository = $friendRepository;
    }

    public function fetchRanking(): ResponseDTO
    {
        return new ResponseDTO($this->friendRepository->fetchRanking());
    }
}
