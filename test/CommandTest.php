<?php
namespace Barberry\Plugin\Imagemagic;

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
    }

//--------------------------------------------------------------------------------------------------

    private static function command($commandString = null)
    {
        $command = new Command();
        return $command->configure($commandString);
    }
}
