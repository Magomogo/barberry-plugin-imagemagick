<?php
namespace Barberry\Plugin\Imagemagick;
use Barberry\Plugin;
use Barberry\ContentType;
use Barberry\Plugin\Imagemagick\Shell\AnimatedGif;
use Barberry\Plugin\Imagemagick\Shell\AnimatedGif\Meta;

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
        $shellCommand = new ShellCommand($command);

        $matches = [];
        preg_match_all('@\[(\d+)\]\sGIF\s(\d+x\d+)\s(.*?)\s(.*?)\s(.*?)\s@s', $bin, $matches);
        if (isset($matches[1], $matches[2]) && !empty($matches[1]) && count($matches[1]) > 1) {
            $shellCommand = new AnimatedGif($command, new Meta($matches));
        }

        $source = tempnam($this->tempPath, "imagemagick_");
        chmod($source, 0664);

        $destination = $source . '.' . $this->targetContentType->standardExtension();
        file_put_contents($source, $bin);

        $imagick = new \Imagick($source);
        $identity = $imagick->identifyImage(true);

        print_r([$identity]);

        exec($shellCommand->makeCommand($source, $destination));

        if (is_file($destination)) {
            $bin = file_get_contents($destination);
            unlink($destination);
        }
        unlink($source);

        return $bin;
    }
}
