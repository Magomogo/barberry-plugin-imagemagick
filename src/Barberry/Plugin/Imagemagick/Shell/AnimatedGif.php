<?php

namespace Barberry\Plugin\Imagemagick\Shell;

use Barberry\Plugin\Imagemagick\Command;
use Barberry\Plugin\Imagemagick\Shell\AnimatedGif\Meta;
use Barberry\Plugin\Imagemagick\ShellCommand;
use Barberry\Plugin\InterfaceCommand;

class AnimatedGif extends ShellCommand
{

    /** @var Meta */
    private $meta;

    /**
     * @param Meta $meta
     * @param Command $command
     */
    public function __construct(Meta $meta, Command $command)
    {
        parent::__construct($command);
        $this->meta = $meta;
    }

    public function makeCommand($source, $destination)
    {
        return sprintf(
            'convert %s %s %s %s',
            '-size ' . $this->meta->getFrame(0)->getSize(),
            $source,
            $this,
            $destination
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $cmd = '-coalesce';

        if ($this->command->width() || $this->command->height()) {
            $cmd .= ' -resize "' . $this->command->width() . 'x' . $this->command->height() .
                ($this->command->noUpscale() ? '>' : '') . '"';
        }

        if ($this->command->background() !== null) {
            $cmd .= ' -background "#' . $this->command->background() . '" -layers optimize';
        }

        if ($this->command->canvasWidth() || $this->command->canvasHeight()) {
            $cmd .= ' -repage ' . $this->command->canvasWidth() . 'x' . $this->command->canvasHeight();
        }

        return trim($cmd);
    }

    /**
     * @param string $source
     * @return bool
     * @throws \ImagickException
     */
    public static function isAnimated($source)
    {
        $imagick = new \Imagick($source);

        $i = 1;
        $imagick->setFirstIterator();
        while ($imagick->nextImage()) {
            $i++;
        }

        return $i > 1;
    }

    /**
     * @param InterfaceCommand $command
     * @param $source
     * @return AnimatedGif
     */
    public static function init($source, InterfaceCommand $command)
    {
        exec('identify ' . escapeshellarg($source), $output);

        $matches = [];
        preg_match_all('@\[(\d+)\]\sGIF\s(\d+x\d+)\s(.*?)\s(.*?)\s(.*?)\s@s', implode(PHP_EOL, $output), $matches);
        return new AnimatedGif(new Meta($matches), $command);

    }
}
