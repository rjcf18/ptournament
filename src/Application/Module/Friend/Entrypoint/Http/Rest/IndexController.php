<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Friend\Entrypoint\Http\Rest;

use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Response;
use PoolTournament\Domain\Module\Friend\FetchInfo\DTO\Request as FriendFetchInfoRequestDTO;
use PoolTournament\Domain\Module\Friend\FetchInfo\Exception\FriendNotFoundException;
use PoolTournament\Domain\Module\Friend\FetchInfo\Service as FriendFetchInfoService;

class IndexController
{
    private FriendFetchInfoService $friendFetchInfoService;

    public function __construct(FriendFetchInfoService $friendFetchInfoService)
    {
        $this->friendFetchInfoService = $friendFetchInfoService;
    }

    /**
     * @param Request $request
     *
     * @throws FriendNotFoundException
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
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
}
