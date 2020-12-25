<?php declare(strict_types=1);
namespace UnitTest\Module\Core\Entrypoint\Yaml;

use PHPUnit\Framework\TestCase;
use PoolTournament\App\Module\Core\Entrypoint\Yaml\Exception\FileNotFoundException;
use PoolTournament\App\Module\Core\Entrypoint\Yaml\Exception\ParseException;
use PoolTournament\App\Module\Core\Entrypoint\Yaml\Parser;

class ParserTest extends TestCase
{
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->fixturesPath = sprintf('%s/Fixtures', __DIR__);
    }

    /**
     * @throws ParseException
     */
    public function testParseWhenFileIsValidReturnsContentsArray(): void
    {
        $filePath = sprintf('%s/valid.yml', $this->fixturesPath);

        $expectedContents = [
            'test1' => [
                'key' => 'value'
            ],
            'test2' => [
                'multiple-key' => [
                    'key1' => 'value1',
                    'key2' => 'value2'
                ]
            ]
        ];

        $parser = new Parser($filePath);
        $yamlContents = $parser->parse();

        $this->assertEquals($expectedContents, $yamlContents);
    }

    /**
     * @throws ParseException
     */
    public function testParseWhenFileNotFoundThrowsException(): void
    {
        $filePath = 'non-existent';

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage(sprintf('%s (%s)', FileNotFoundException::MESSAGE, $filePath));

        $parser = new Parser($filePath);
        $parser->parse();
    }

    /**
     * @throws ParseException
     */
    public function testParseWhenProblemParsingFileOccursThrowsException(): void
    {
        $filePath = sprintf('%s/invalid.yml', $this->fixturesPath);

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage(sprintf('%s (%s)', ParseException::MESSAGE, $filePath));

        $parser = new Parser($filePath);
        $parser->parse();
    }
}