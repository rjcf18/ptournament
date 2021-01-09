<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Match\Entrypoint\Http\Rest;

use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Response;
use PoolTournament\Domain\Module\Match\FetchInfo\DTO\Request as MatchFetchInfoRequestDTO;
use PoolTournament\Domain\Module\Match\FetchInfo\Service as MatchFetchInfoService;
use Throwable;

class IndexController
{
    private MatchFetchInfoService $matchFetchInfoService;

    public function __construct(MatchFetchInfoService $matchFetchInfoService)
    {
        $this->matchFetchInfoService = $matchFetchInfoService;
    }

    public function indexAction(Request $request): Response
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
        return (new Response(200))->setBody(['message' => "Match result submission page"]);
    }
}
