<?php
namespace PoolTournament\Application\Module\Match\Entrypoint;

use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Response;
use PoolTournament\Domain\Module\Match\FetchInfo\Exception\MatchNotFoundException;
use Throwable;

class ErrorResponseFactory
{
    public static function create(Throwable $throwable): Response
    {
        switch ($throwable::class) {
            case MatchNotFoundException::class:
                $response = new Response(422);
                break;
            default:
                $response = new Response(500);
                break;
        }

        return $response->setBody([
            'code' => $response->getCode(),
            'error' => $throwable->getMessage()
        ]);
    }
}
