<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Match\Entrypoint\Http\Rest;

use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Response;
use PoolTournament\Domain\Module\Match\FetchInfo\DTO\Request as MatchFetchInfoRequestDTO;
use PoolTournament\Domain\Module\Match\FetchInfo\Service as MatchFetchInfoService;
use PoolTournament\Domain\Module\Match\Creation\Service as MatchCreationService;
use PoolTournament\Domain\Module\Match\FetchList\DTO\Request as MatchFetchListRequestDTO;
use PoolTournament\Domain\Module\Match\FetchList\Service as MatchFetchListService;
use Throwable;

class IndexController
{
    private MatchFetchInfoService $matchFetchInfoService;
    private MatchCreationService $matchCreationService;
    private MatchFetchListService $matchFetchListService;

    public function __construct(
        MatchFetchInfoService $matchFetchInfoService,
        MatchCreationService $matchCreationService,
        MatchFetchListService $matchFetchListService
    ) {
        $this->matchFetchInfoService = $matchFetchInfoService;
        $this->matchCreationService = $matchCreationService;
        $this->matchFetchListService = $matchFetchListService;
    }

    public function infoAction(Request $request): Response
    {
        try {
            $namedParameters = $request->getNamedParameters();
            $matchId = (int) $namedParameters['id'];

            return (new Response(200))->setBody(
                MatchArrayBuilder::build(
                    $this->matchFetchInfoService->fetchInfo(
                        new MatchFetchInfoRequestDTO($matchId)
                    )->getMatch()
                )
            );
        } catch (Throwable $throwable) {
            return ErrorResponseFactory::create($throwable);
        }
    }

    public function resultAction(Request $request): Response
    {
        try {
            $requestBody = $request->getBody();

            return (new Response(200))->setBody(
                MatchArrayBuilder::build(
                    $this->matchCreationService->create(
                        CreationRequestDtoFactory::create($requestBody)
                    )->getMatch()
                )
            );
        } catch (Throwable $throwable) {
            return ErrorResponseFactory::create($throwable);
        }
    }

    public function listAction(Request $request): Response
    {
        return (new Response(200))->setBody(
            MatchCollectionArrayBuilder::build(
                $this->matchFetchListService->fetchList(
                    (new MatchFetchListRequestDTO())
                )->getMatchCollection()
            )
        );
    }
}
