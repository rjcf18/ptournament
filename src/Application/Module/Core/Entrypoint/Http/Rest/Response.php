<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Entrypoint\Http\Rest;

class Response
{
    private int $code;
    private array $body = [];
    private array $headers = [];

    public function __construct(int $code)
    {
        $this->code = $code;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function setBody(array $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }
}