<?php declare(strict_types=1);
namespace PoolTournament\App\Module\Core\Entrypoint\Http\Rest;

class Request
{
    private string $method;
    private string $uri;
    private array $body = [];
    private array $headers = [];
    private array $queryParameters = [];
    private array $namedParameters = [];

    public function __construct(string $method, string $uri)
    {
        $this->method = $method;
        $this->uri = $uri;
    }

    public static function createFromGlobals(): self
    {
        $request = new self(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            strtok($_SERVER['REQUEST_URI'], '?')
        );

        if (!empty($_GET)) {
            $request->setQueryParameters($_GET);
        }

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, "HTTP_")) {
                $headers[$key] = $value;
            }
        }

        $request->setHeaders($headers);

        $request->setBody(
            !empty($_POST)
                ? $_POST
                : json_decode(file_get_contents('php://input'), true) ?? []
        );

        return $request;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function setBody(array $body): void
    {
        $this->body = $body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }

    public function setQueryParameters(array $queryParameters): void
    {
        $this->queryParameters = $queryParameters;
    }

    public function getNamedParameters(): array
    {
        return $this->namedParameters;
    }

    public function setNamedParameters(array $namedParameters): void
    {
        $this->namedParameters = $namedParameters;
    }
}