<?php declare(strict_types=1);
namespace PoolTournament\App\Module\Match\Entrypoint;

use PoolTournament\App\Module\Core\Entrypoint\Http\Rest\Request;

class IndexController
{
    public function indexAction(Request $request)
    {
        echo "Match details page for ID: ". $request->getNamedParameters()['id'];
    }

    public function resultAction(Request $request)
    {
        echo "Match result submission page";
    }
}