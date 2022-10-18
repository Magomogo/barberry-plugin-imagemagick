<?php
namespace Barberry\Plugin\Imagemagick;
use Barberry\ContentType;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testRemovesColorProfileInformation()
    {
        $bin = self::converter()->convert(file_get_contents(__DIR__ . '/data/colorProfile.jpeg'), self::command('strip'));
        $tmpFile = self::tmpDir() . 'profilesCheckRemove.jpg';
        @unlink($tmpFile);
        file_put_contents($tmpFile, $bin);
        $this->assertEquals('', exec('identify -verbose "' . $tmpFile . '" | grep "Profile-"'));
        unlink($tmpFile);
    }

    public function testKeepsColorProfileInformation()
    {
        $bin = self::converter()->convert(file_get_contents(__DIR__ . '/data/colorProfile.jpeg'), self::command(''));
        $tmpFile = self::tmpDir() . 'profilesCheckKeep.jpg';
        @unlink($tmpFile);
        file_put_contents($tmpFile, $bin);
        $this->assertEquals('    Profile-xmp: 16763 bytes', exec('identify -verbose "' . $tmpFile . '" | grep "Profile-"'));
        unlink($tmpFile);
    }

    public function testConvertsGifToJpegWithResizing()
    {
        $bin = self::converter()->convert(file_get_contents(__DIR__ . '/data/1x1.gif'), self::command('10x10'));
        $this->assertEquals(ContentType::jpeg(), ContentType::byString($bin));
    }

    public function testNoUpscaleDoesNoChangeSmallGIF()
    {
        $binInput = file_get_contents(__DIR__ . '/data/1x1.gif');
        $binOutput = self::converter()->convert($binInput, self::command('1000x1000noUpscale'));
        $image = imagecreatefromstring($binOutput);
        $this->assertEquals(1, imagesx($image));
        $this->assertEquals(1, imagesy($image));
    }

    public function testConvertsGifToJpegWithResizingAndBackgroundAndCanvasAndQuality()
    {
        $bin = self::converter()->convert(
            file_get_contents(__DIR__ . '/data/1x1.gif'),
            self::command('10x10bgFF00FFcanvas20x20quality41')
        );
        $this->assertEquals(ContentType::jpeg(), ContentType::byString($bin));
    }

    private static function converter()
    {
        $converter = new Converter;
        return $converter->configure(ContentType::jpeg(), self::tmpDir());
    }

    private static function tmpDir() {
        return __DIR__ . '/../tmp/';
    }

    private static function command($config)
    {
        $command = new Command();
        $command->configure($config);
        return $command;
    }
}
