<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\FetchInfo;

use PoolTournament\Domain\Module\Match\FetchInfo\DTO\Request as RequestDTO;
use PoolTournament\Domain\Module\Match\FetchInfo\DTO\Response as ResponseDTO;
use PoolTournament\Domain\Module\Match\FetchInfo\Exception\MatchNotFoundException;

class Service
{
    private MatchRepository $matchRepository;

    public function __construct(MatchRepository $matchRepository)
    {
        $this->matchRepository = $matchRepository;
    }

    /**
     * @param RequestDTO $request
     *
     * @throws MatchNotFoundException
     *
     * @return ResponseDTO
     */
    public function fetchInfo(RequestDTO $request): ResponseDTO
    {
        $match = $this->matchRepository->getById($request->getMatchId());

        if (empty($match)) {
            throw new MatchNotFoundException();
        }

        return new ResponseDTO($match);
    }
}
