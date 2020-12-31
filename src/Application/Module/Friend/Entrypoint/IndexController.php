<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Friend\Entrypoint;

use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;
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
     */
    public function indexAction(Request $request)
    {
        $namedParameters = $request->getNamedParameters();
        $friendId = (int) $namedParameters['id'];

        echo json_encode(
            FriendArrayBuilder::build(
                $this->friendFetchInfoService->fetchInfo(
                    new FriendFetchInfoRequestDTO($friendId)
                )->getFriend()
            )
        );
    }
}