<?php
namespace Barberry\Plugin\Imagemagick;
use Barberry\Plugin;
use Barberry\ContentType;
use Barberry\Plugin\Imagemagick\Shell\AnimatedGif;

class Converter implements Plugin\InterfaceConverter
{
    /**
     * @var string
     */
    private $tempPath;

    /**
     * @var ContentType
     */
    private $targetContentType;

    public function configure(ContentType $targetContentType, $tempPath)
    {
        $this->tempPath = $tempPath;
        $this->targetContentType = $targetContentType;
        return $this;
    }

    public function convert($bin, Plugin\InterfaceCommand $command = null)
    {
        if (!isset($command)) {
            $command = new Command();
        }

        $shellCommand = new ShellCommand($command);

        $source = tempnam($this->tempPath, "imagemagick_");
        chmod($source, 0664);

        $destination = $source . '.' . $this->targetContentType->standardExtension();
        file_put_contents($source, $bin);

        if (AnimatedGif::isAnimated($source)) {
            $shellCommand = AnimatedGif::init($source, $command);
        }

        exec($shellCommand->makeCommand($source, $destination));

        if (is_file($destination)) {
            $bin = file_get_contents($destination);
            unlink($destination);
        }
        unlink($source);

        return $bin;
    }
}
