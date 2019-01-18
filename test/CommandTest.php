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

    /**
     * @param string $strCommand
     * @param string $color
     * @param string $fuzz
     * @dataProvider dpTestOnlyTrimColor
     */
    public function testTrim($strCommand, $color, $fuzz)
    {
        $command = self::command($strCommand);
        $this->assertEquals($color, $command->trimColor());
        $this->assertEquals($fuzz, $command->trimFuzz());
        $this->assertEquals($strCommand, (string) $command);
    }

    public function dpTestOnlyTrimColor()
    {
        return [
            [
                'trimAABBCC',
                'AABBCC',
                ''
            ],
            [
                'trimFFF',
                'FFF',
                ''
            ],
            [
                'trimAABBCCx2',
                'AABBCC',
                '2'
            ],
            [
                'trimFFFx50',
                'FFF',
                '50'
            ],
            [
                'trimx10',
                '',
                '10'
            ],
            [
                'trim',
                '',
                ''
            ]
        ];
    }

    public function testCanConstructSameUrlUrlWithAllParams()
    {
        $command = '200x100bgFF00FFcanvas300x200quality88colorspaceGraytrimAABBCCx1';
        $this->assertEquals($command, strval(self::command($command)));
    }

    public function testCanConstructSameUrlUrlWithAllParamsAndSimpleTrim()
    {
        $command = '200x100bgFF00FFcanvas300x200quality88colorspaceGraytrim';
        $this->assertEquals($command, strval(self::command($command)));
    }

//--------------------------------------------------------------------------------------------------

    private static function command($commandString = null)
    {
        $command = new Command();
        return $command->configure($commandString);
    }
}
