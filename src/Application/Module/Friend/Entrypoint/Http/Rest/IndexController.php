<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Friend\Entrypoint\Http\Rest;

use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Response;
use PoolTournament\Domain\Module\Friend\FetchInfo\DTO\Request as FriendFetchInfoRequestDTO;
use PoolTournament\Domain\Module\Friend\FetchInfo\Exception\FriendNotFoundException;
use PoolTournament\Domain\Module\Friend\FetchInfo\Service as FriendFetchInfoService;
use PoolTournament\Domain\Module\Friend\FetchRanking\Service as FriendFetchRankingService;
use PoolTournament\Domain\Module\Match\FetchList\DTO\Request as MatchFetchListRequestDTO;
use PoolTournament\Domain\Module\Match\FetchList\Service as MatchFetchListService;

class IndexController
{
    private FriendFetchInfoService $friendFetchInfoService;
    private MatchFetchListService $matchFetchListService;
    private FriendFetchRankingService $friendFetchRankingService;

    public function __construct(
        FriendFetchInfoService $friendFetchInfoService,
        MatchFetchListService $matchFetchListService,
        FriendFetchRankingService $friendFetchRankingService
    ) {
        $this->friendFetchInfoService = $friendFetchInfoService;
        $this->matchFetchListService = $matchFetchListService;
        $this->friendFetchRankingService = $friendFetchRankingService;
    }

    /**
     * @param Request $request
     *
     * @throws FriendNotFoundException
     *
     * @return Response
     */
    public function infoAction(Request $request): Response
    {
        $namedParameters = $request->getNamedParameters();
        $friendId = (int) $namedParameters['id'];

        return (new Response(200))->setBody(
            FriendArrayBuilder::build(
                $this->friendFetchInfoService->fetchInfo(
                    new FriendFetchInfoRequestDTO($friendId)
                )->getFriend()
            )
        );
    }

    public function matchesAction(Request $request): Response
    {
        $namedParameters = $request->getNamedParameters();
        $friendId = (int) $namedParameters['id'];

        return (new Response(200))->setBody(
            MatchCollectionArrayBuilder::build(
                $this->matchFetchListService->fetchList(
                    (new MatchFetchListRequestDTO())->setFriendId($friendId)
                )->getMatchCollection()
            )
        );
    }

    public function rankingAction(Request $request): Response
    {
        return (new Response(200))->setBody(
            FriendCollectionArrayBuilder::build(
                $this->friendFetchRankingService->fetchRanking()->getFriendCollection()
            )
        );
    }
}
