<?php

namespace Barberry\Plugin\Imagemagick\Shell;

use Barberry\Plugin\Imagemagick\Command;
use Barberry\Plugin\Imagemagick\Shell\AnimatedGif\Meta;
use Barberry\Plugin\Imagemagick\ShellCommand;

class AnimatedGif extends ShellCommand
{

    /** @var Meta */
    private $meta;

    /**
     * @param Command $command
     * @param Meta $meta;
     */
    public function __construct(Command $command, Meta $meta)
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

}
