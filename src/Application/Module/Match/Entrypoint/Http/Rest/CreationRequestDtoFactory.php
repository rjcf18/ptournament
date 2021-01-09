<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Match\Entrypoint\Http\Rest;

use DateTimeImmutable;
use PoolTournament\Domain\Module\Match\Creation\DTO\Request as RequestDTO;

class CreationRequestDtoFactory
{
    public static function create(array $requestBody): RequestDTO
    {
        return new RequestDTO(
            (int) $requestBody['winner_id'],
            (int) $requestBody['looser_id'],
            (int) $requestBody['looser_balls_left'],
            (bool) $requestBody['absence'],
            DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $requestBody['date']),
        );
    }
}
