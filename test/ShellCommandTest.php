<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 18.06.2013
 * Time: 4:45 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Barberry\Plugin\Imagemagick;


class ShellCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyCommand()
    {
        $this->assertEquals('-auto-orient', self::shellCommandString(''));
    }

    public function testResizeOnly()
    {
        $this->assertEquals('-auto-orient -resize 150x100', self::shellCommandString('150x100'));
    }

    public function testResizeAndBackground()
    {
        $this->assertEquals(
            '-auto-orient -resize 150x100 -background "#FF00FF" -flatten',
            self::shellCommandString('150x100bgFF00FF')
        );
    }

    public function testResizeAndBackgroundAndCanvas()
    {
        $this->assertEquals(
            '-auto-orient -resize 150x100 -background "#FF00FF" -flatten -size 250x150 xc:#FF00FF +swap -gravity center -composite',
            self::shellCommandString('150x100bgFF00FFcanvas250x150')
        );
    }

    public function testResizeAndCanvasOnlyUseBlackBackground()
    {
        $this->assertEquals(
            '-auto-orient -resize 150x100 -size 250x150 xc:#000000 +swap -gravity center -composite',
            self::shellCommandString('150x100canvas250x150')
        );
    }

    private static function shellCommandString($string)
    {
        $command = new Command();
        $command->configure($string);
        $shellCommand = new ShellCommand($command);
        return strval($shellCommand);
    }
}