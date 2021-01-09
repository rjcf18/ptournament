<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Tournament\Entrypoint\Http\Rest;

use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Response;

class IndexController
{
    public function indexAction(Request $request): Response
    {
        return (new Response(200))->setBody(['message' => "Pool Tournaments Homepage"]);
    }
}
