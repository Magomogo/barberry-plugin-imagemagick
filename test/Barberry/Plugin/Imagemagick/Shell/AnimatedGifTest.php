<?php

namespace Barberry\Plugin\Imagemagick\Barberry\Plugin\Imagemagick\Shell;

use Barberry\Plugin\Imagemagick\Command;
use Barberry\Plugin\Imagemagick\Shell\AnimatedGif;
use Barberry\Plugin\Imagemagick\Shell\AnimatedGif\Meta;

class AnimatedGifTest extends \PHPUnit_Framework_TestCase
{

    private static function shellCommandString($string)
    {
        $command = new Command();
        $command->configure($string);

        $shellCommand = new AnimatedGif($command, new Meta([
            [],
            [0, 1],
            ['100x100', '100x100'],
            ['100x100+0+0', '100x100+0+0'],
            ['8-bit', '8-bit'],
            ['sRGB', 'sRGB'],
        ]));

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
}
