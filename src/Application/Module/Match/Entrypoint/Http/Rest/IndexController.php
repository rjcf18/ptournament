<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Match\Entrypoint\Http\Rest;

use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Response;
use PoolTournament\Domain\Module\Match\FetchInfo\DTO\Request as MatchFetchInfoRequestDTO;
use PoolTournament\Domain\Module\Match\FetchInfo\Service as MatchFetchInfoService;
use PoolTournament\Domain\Module\Match\Creation\Service as MatchCreationService;
use Throwable;

class IndexController
{
    private MatchFetchInfoService $matchFetchInfoService;
    private MatchCreationService $matchCreationService;

    public function __construct(
        MatchFetchInfoService $matchFetchInfoService,
        MatchCreationService $matchCreationService
    ) {
        $this->matchFetchInfoService = $matchFetchInfoService;
        $this->matchCreationService = $matchCreationService;
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
}
