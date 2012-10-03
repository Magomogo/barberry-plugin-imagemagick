<?php
namespace Barberry\Plugin\Imagemagic;
use Barberry\Direction;
use Barberry\Direction\Composer;

class InstallerTest extends \PHPUnit_Framework_TestCase
{
    private $directionDir;

    protected function setUp()
    {
        $this->directionDir = realpath(__DIR__ . '/../tmp' ) . '/test-directions/';
        @mkdir($this->directionDir, 0777, true);
    }

    protected function tearDown()
    {
        exec("rm -rf " . $this->directionDir);
    }

    public function testInstallsGIFtoJPGDirection()
    {
        $installer = new Installer('/tmp/');
        $installer->install(new Composer($this->directionDir));

        include $this->directionDir . 'GifToJpg.php';
        $jpgToGif = new Direction\GifToJpgDirection('');
        $this->assertNotNull($jpgToGif->convert(file_get_contents(__DIR__ . '/data/1x1.gif')));
    }
}
