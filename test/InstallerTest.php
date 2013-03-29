<?php
namespace Barberry\Plugin\Imagemagick;
use Barberry\Direction;
use Barberry\Direction\Composer;
use Barberry\Monitor;

class InstallerTest extends \PHPUnit_Framework_TestCase
{
    private $directionDir;
    private $monitorDir;

    protected function setUp()
    {
        $this->directionDir = realpath(__DIR__ . '/../tmp' ) . '/test-directions/';
        $this->monitorDir = realpath(__DIR__ . '/../tmp') . '/test-monitors/';
        @mkdir($this->directionDir, 0777, true);
        @mkdir($this->monitorDir, 0777, true);
    }

    protected function tearDown()
    {
        exec("rm -rf " . $this->directionDir);
        exec("rm -rf " . $this->monitorDir);
    }

    public function testInstallsGIFtoJPGDirection()
    {
        $installer = new Installer();
        $installer->install(new Composer($this->directionDir, '/tmp/'), new Monitor\Composer($this->monitorDir, '/tmp/'));

        include $this->directionDir . 'GifToJpg.php';
        $jpgToGif = new Direction\DirectionGifToJpg('');
        $this->assertNotNull($jpgToGif->convert(file_get_contents(__DIR__ . '/data/1x1.gif')));
    }
}
