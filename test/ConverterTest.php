<?php
namespace Barberry\Plugin\Imagemagic;
use Barberry\ContentType;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertsGitToJpegWithResizing()
    {
        $bin = self::converter()->convert(file_get_contents(__DIR__ . '/data/1x1.gif'), self::resize10x10Command());
        $this->assertEquals(ContentType::jpeg(), ContentType::byString($bin));
    }

    private static function converter()
    {
        $converter = new Converter;
        return $converter->configure(ContentType::jpeg(), __DIR__ . '/../tmp/');
    }

    private static function resize10x10Command()
    {
        $command = new Command();
        $command->configure('10x10');
        return $command;
    }
}
