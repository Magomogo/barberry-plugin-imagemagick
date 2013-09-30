<?php
namespace Barberry\Plugin\Imagemagick;
use Barberry\ContentType;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertsGifToJpegWithResizing()
    {
        $bin = self::converter()->convert(file_get_contents(__DIR__ . '/data/1x1.gif'), self::resize10x10Command());
        $this->assertEquals(ContentType::jpeg(), ContentType::byString($bin));
    }

    public function testConvertsGifToJpegWithResizingAndBackgroundAndCanvasAndQuality()
    {
        $bin = self::converter()->convert(
            file_get_contents(__DIR__ . '/data/1x1.gif'),
            self::resize10x10bgFF00FFcanvas20x20quality41Command()
        );
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

    private static function resize10x10bgFF00FFcanvas20x20quality41Command()
    {
        $command = new Command();
        $command->configure('10x10bgFF00FFcanvas20x20quality41');
        return $command;
    }
}
