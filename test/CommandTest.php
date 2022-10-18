<?php
namespace Barberry\Plugin\Imagemagick;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testNoWidthByDefault()
    {
        $this->assertNull(self::command()->width());
    }

    public function testNoHeightByDefault()
    {
        $this->assertNull(self::command()->height());
    }

    public function testReadsWidthOnly()
    {
        $this->assertEquals(123, self::command('123x')->width());
        $this->assertNull(self::command()->height());
    }

    public function testReadsHeightOnly()
    {
        $this->assertNull(self::command()->width());
        $this->assertEquals(123, self::command('x123')->height());
    }

    public function testReadsBothWidthAndHeight()
    {
        $command = self::command('321x123');
        $this->assertEquals(321, $command->width());
        $this->assertEquals(123, $command->height());
    }

    public function testAmbiguityTest()
    {
        $this->assertFalse(self::command('200sda2x100')->conforms('200x100'));
        $this->assertTrue(self::command('')->conforms(''));
    }

    public function testReplaceInitialSizesIntoMaxAndMin()
    {
        $command = self::command('9000x9000');
        $this->assertEquals(800, $command->width());
        $this->assertEquals(600, $command->height());
    }

    public function testInitialCommandStringEqualsObjectToStringConvertion()
    {
        $command = self::command('9000x9000');
        $this->assertSame(strval($command),'9000x9000');
    }

    public function testNoBackgroundByDefault()
    {
        $this->assertNull(self::command('100x100')->background());
    }

    public function testReadsBackgroundOnly()
    {
        $this->assertEquals('FFCCFF', self::command('bgFFCCFF')->background());
    }

    public function testReadsShortBackgroundOnly()
    {
        $this->assertEquals('FFF', self::command('bgFFF')->background());
    }

    public function testReadsQualityOnly()
    {
        $this->assertEquals(55, self::command('quality55')->quality());
    }

    public function testReadsColorSpaceOnly()
    {
        $this->assertEquals('Gray', self::command('colorspaceGray')->colorspace());
    }

    public function testReadsBackgroundAndResizeBoth()
    {
        $command = self::command('100x500bgFFCCFF');
        $this->assertEquals(100, $command->width());
        $this->assertEquals(500, $command->height());
        $this->assertEquals('FFCCFF', $command->background());
    }

    public function testReadsCanvasWidthAndHeigh()
    {
        $command = self::command('canvas200x100');
        $this->assertEquals(200, $command->canvasWidth());
        $this->assertEquals(100, $command->canvasHeight());
    }

    public function testCanvasWidthAndHeightNullByDefault()
    {
        $command = self::command('200x100');
        $this->assertNull($command->canvasWidth());
        $this->assertNull($command->canvasHeight());
    }

    public function testFullTrimMod()
    {
        $command = self::command('trimAABBCCx2');
        $this->assertEquals('AABBCC', $command->trimColor());
        $this->assertEquals('2', $command->trimFuzz());
    }

    public function testOnlyTrimColor()
    {
        $command = self::command('trimAABBCCx');
        $this->assertEquals('AABBCC', $command->trimColor());
        $this->assertEquals('', $command->trimFuzz());
    }

    public function testOnlyTrimFuzz()
    {
        $command = self::command('trimx10');
        $this->assertEquals('', $command->trimColor());
        $this->assertEquals('10', $command->trimFuzz());
    }

    public function testSimpleTrim()
    {
        $command = self::command('trimx');
        $this->assertEquals('trimx', strval($command));
    }

    public function testCanConstructSameUrlUrlWithAllParams()
    {
        $this->assertEquals(
            '200x100bgFF00FFcanvas300x200quality88colorspaceGraytrimAABBCCx1',
            strval(self::command('200x100bgFF00FFcanvas300x200quality88colorspaceGraytrimAABBCCx1'))
        );
    }

//--------------------------------------------------------------------------------------------------

    private static function command($commandString = null)
    {
        $command = new Command();
        return $command->configure($commandString);
    }
}
