<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Tournament\Entrypoint;

use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;

class IndexController
{
    public function indexAction(Request $request)
    {
        echo "Pool Tournaments Homepage";
    }
}