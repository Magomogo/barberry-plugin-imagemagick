<?php

namespace Barberry\Plugin\Imagemagick\Shell;

use Barberry\Plugin\Imagemagick\Command;

class AnimatedGifTest extends \PHPUnit_Framework_TestCase
{

    private static function shellCommandString($string)
    {
        $command = new Command();
        $command->configure($string);

        $shellCommand = AnimatedGif::init(__DIR__ . '/../../../../data/animated.gif', $command);

        return (string) $shellCommand;
    }

    /**
     * @param $command
     * @param $shellParams
     * @dataProvider dpTestShellWorks
     */
    public function testShellWorks($command, $shellParams)
    {
        $cmd = self::shellCommandString($command);
        $this->assertEquals($shellParams, $cmd);
    }

    public function dpTestShellWorks()
    {
        return [
            [
                '', '-coalesce'
            ],
            [
                '150x100', '-coalesce -resize "150x100"'
            ],
            [
                'bgFFF', '-coalesce -background "#FFF" -layers optimize'
            ],
            [
                'canvas100x150', '-coalesce -repage 100x150'
            ],
            [
                '150x150bgFFFcanvas100x150',
                '-coalesce -resize "150x150" -background "#FFF" -layers optimize -repage 100x150'
            ]
        ];
    }

    /**
     * @param $fileName
     * @param $animated
     * @return void
     * @throws \ImagickException
     * @dataProvider dpTestIsGifAnimated
     */
    public function testIsGifAnimated($fileName, $animated)
    {
        self::assertEquals($animated, AnimatedGif::isAnimated($fileName));
    }

    public function dpTestIsGifAnimated()
    {
        return [
            [__DIR__ . '/../../../../data/animated.gif', true],
            [__DIR__ . '/../../../../data/1x1.gif', false],
            [__DIR__ . '/../../../../data/static.gif', false],
            [__DIR__ . '/../../../../data/colorProfile.jpeg', false],
        ];
    }
}
