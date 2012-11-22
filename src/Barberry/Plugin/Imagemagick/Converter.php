<?php
namespace Barberry\Plugin\Imagemagick;
use Barberry\Plugin;
use Barberry\ContentType;

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
        $resize = ($command->width() || $command->height()) ?
            '-resize ' . $command->width() . 'x' . $command->height() : '';
        $source = tempnam($this->tempPath, "imagemagick_");
        chmod($source, 0664);
        $destination = $source . '.' . $this->targetContentType->standardExtension();
        file_put_contents($source, $bin);
        exec(
            'convert -auto-orient ' . $resize . ' ' . $source . ' ' . $destination
        );
        if (is_file($destination)) {
            $bin = file_get_contents($destination);
            unlink($destination);
        }
        unlink($source);

        return $bin;
    }
}
