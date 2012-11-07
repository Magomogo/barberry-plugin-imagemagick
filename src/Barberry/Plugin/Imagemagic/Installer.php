<?php
namespace Barberry\Plugin\Imagemagic;

use Barberry\Plugin;
use Barberry\Direction;
use Barberry\Monitor;
use Barberry\ContentType;

class Installer implements Plugin\InterfaceInstaller
{
    public function install(Direction\ComposerInterface $directionComposer, Monitor\ComposerInterface $monitorComposer,
        $pluginParams = array())
    {
        foreach (self::directions() as $pair) {
            $directionComposer->writeClassDeclaration(
                $pair[0],
                $pair[1],
                'new Plugin\\Imagemagic\\Converter',
                'new Plugin\\Imagemagic\\Command'
            );
        }

        $monitorComposer->writeClassDeclaration('Imagemagic');
    }

//--------------------------------------------------------------------------------------------------

    private static function directions()
    {
        return array(
            array(ContentType::jpeg(), \Barberry\ContentType::gif()),
            array(ContentType::jpeg(), \Barberry\ContentType::png()),
            array(ContentType::jpeg(), \Barberry\ContentType::jpeg()),
            array(ContentType::gif(), \Barberry\ContentType::jpeg()),
            array(ContentType::gif(), \Barberry\ContentType::png()),
            array(ContentType::gif(), \Barberry\ContentType::gif()),
            array(ContentType::png(), \Barberry\ContentType::jpeg()),
            array(ContentType::png(), \Barberry\ContentType::gif()),
            array(ContentType::png(), \Barberry\ContentType::png()),
        );
    }
}
