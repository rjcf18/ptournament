<?php declare(strict_types=1);
namespace UnitTest\Module\Core\Entrypoint\Http\Rest\Routing\Fixtures;

class TestController
{
    public function indexAction(): array
    {
        return func_get_args();
    }

    public function testAction(): array
    {
        return func_get_args();
    }
}