<?php declare(strict_types=1);
namespace UnitTest\Application\Module\Core\Entrypoint\Http\Rest;

use PHPUnit\Framework\TestCase;
use PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Request;

class RequestTest extends TestCase
{
    public function testCreateFromGlobals()
    {
        $_SERVER = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/localhost/test/50?param=value',
            'HTTP_CONTENT_TYPE' => 'application/json',
        ];

        $_GET = [
            'param' => 'value'
        ];

        $_POST = [
            'var1' => 'value1'
        ];

        $request = (new Request('POST', '/localhost/test/50'))
            ->setBody(['var1' => 'value1'])
            ->setHeaders(['CONTENT_TYPE' => 'application/json'])
            ->setQueryParameters(['param' => 'value']);

        $this->assertEquals($request, Request::createFromGlobals());
    }
}