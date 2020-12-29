<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Friend\Entrypoint;

use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;

class IndexController
{
    public function indexAction(Request $request)
    {
        echo "Friend details page for ID: " . $request->getNamedParameters()['id'];
    }
}