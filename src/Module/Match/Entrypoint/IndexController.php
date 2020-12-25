<?php declare(strict_types=1);
namespace PoolTournament\App\Module\Match\Entrypoint;

class IndexController
{
    public function indexAction()
    {
        echo "Match details page";
    }

    public function resultAction()
    {
        echo "Match result submission page";
    }
}