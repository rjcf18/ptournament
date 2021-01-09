<?php
namespace PoolTournament\Application\Module\Match\Entrypoint\Http\Rest;

use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Response;
use PoolTournament\Domain\Module\Match\Creation\Exception\MatchCreationErrorException;
use PoolTournament\Domain\Module\Match\FetchInfo\Exception\MatchNotFoundException;
use Throwable;

class ErrorResponseFactory
{
    public static function create(Throwable $throwable): Response
    {
        switch ($throwable::class) {
            case MatchNotFoundException::class:
            case MatchCreationErrorException::class:
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
