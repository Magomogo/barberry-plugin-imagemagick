<?php
namespace Barberry\Plugin\Imagemagick;
use Barberry\ContentType;

class ConverterTest extends \PHPUnit\Framework\TestCase
{
    /** @dataProvider jpegImages */
    public function testRemovesColorProfileInformation($filename)
    {
        $bin = self::converter()->convert(self::input($filename), self::command('strip'));
        $tmpFile = self::tmpDir() . 'profilesCheckRemove.jpg';
        @unlink($tmpFile);
        file_put_contents($tmpFile, $bin);
        $this->assertEquals('', exec('identify -verbose "' . $tmpFile . '" | grep "Profile-"'));
        unlink($tmpFile);
    }

    /** @dataProvider jpegImages */
    public function testKeepsColorProfileInformation($filename)
    {
        $bin = self::converter()->convert(self::input($filename), self::command(''));
        $tmpFile = self::tmpDir() . 'profilesCheckKeep.jpg';
        @unlink($tmpFile);
        file_put_contents($tmpFile, $bin);
        $this->assertStringContainsString('Profile-xmp:', exec('identify -verbose "' . $tmpFile . '" | grep "Profile-"'));
        unlink($tmpFile);
    }

    /** @dataProvider gifImages */
    public function testConvertsGifToJpegWithResizing($filename)
    {
        $bin = self::converter()->convert(self::input($filename), self::command('10x10'));
        $this->assertSame('image/jpeg', getimagesizefromstring($bin)['mime']);
    }

    /** @dataProvider gifImages */
    public function testNoUpscaleDoesNoChangeSmallGIF($filename)
    {
        $binInput = self::input($filename);
        $binOutput = self::converter()->convert($binInput, self::command('1000x1000noUpscale'));
        $image = imagecreatefromstring($binOutput);
        $this->assertEquals(1, imagesx($image));
        $this->assertEquals(1, imagesy($image));
    }

    /** @dataProvider gifImages */
    public function testConvertsGifToJpegWithResizingAndBackgroundAndCanvasAndQuality($filename)
    {
        $bin = self::converter()->convert(
            self::input($filename),
            self::command('10x10bgFF00FFcanvas20x20quality41')
        );
        $this->assertSame('image/jpeg', getimagesizefromstring($bin)['mime']);
    }

    /** @dataProvider jpegImages */
    public function testConvertsJpegWithResizing($filename)
    {
        $input = self::input($filename);

        $output = self::converter()->convert($input, self::command('100x100'));

        $image = getimagesizefromstring($output);
        $this->assertSame('image/jpeg', $image['mime']);
        $this->assertSame(100, $image[0]);
        $this->assertSame(100, $image[1]);
        $this->assertNotSame($input, $output);
        $this->assertEmpty(glob(self::tmpDir() . 'imagemagick_*'));
    }

    public static function gifImages()
    {
        return array(
            'small file' => array('1x1.gif'),
            '3 mb file' => array('3mb.gif')
        );
    }

    public static function jpegImages()
    {
        return array(
            'small file' => array('colorProfile.jpeg'),
            '3 mb file' => array('colorProfile3mb.jpeg')
        );
    }

    private static function input($filename)
    {
        return file_get_contents(__DIR__ . '/data/' . $filename);
    }

    private static function converter($tmpDir = null)
    {
        $converter = new Converter;
        return $converter->configure(ContentType::jpeg(), is_null($tmpDir) ? self::tmpDir() : $tmpDir);
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
