<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 18.06.2013
 * Time: 4:41 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Barberry\Plugin\Imagemagick;


class ShellCommand {

    /**
     * @var Command
     */
    private $command;

    /**
     * @param Command $command
     */
    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $string = '-auto-orient';
        if (!is_null($this->command->colorspace())) {
            $string .= ' -colorspace ' . $this->command->colorspace();
        }
        if ($this->command->width() || $this->command->height()) {
            $string .= ' -resize "' . $this->command->width() . 'x' . $this->command->height() . ($this->command->noUpscale() ? '>' : '') . '"';
        }
        if (!is_null($this->command->background())) {
            $string .= ' -background "#' . $this->command->background() . '" -flatten';
        }
        if ($this->command->canvasWidth() || $this->command->canvasHeight()) {
            $string .=
                ' -size ' . $this->command->canvasWidth() . 'x' . $this->command->canvasHeight() .
                ' xc:' . $this->canvasColor() . ' +swap -gravity center -compose src-over -composite';
        }
        if (!is_null($this->command->quality())) {
            $string .= ' -quality ' . $this->command->quality();
        }
        if (!is_null($this->command->trimColor()) || !is_null($this->command->trimFuzz())) {
            $string .=
                ($this->command->trimColor() ? " -bordercolor \"#{$this->command->trimColor()}\" -border 1x1" : "") .
                ($this->command->trimFuzz() ? " -fuzz {$this->command->trimFuzz()}%" : "") .
                " -trim +repage";
        }
        if ($this->command->stripColorProfiles()) {
            $string .= ' -strip';
        }
        return $string;
    }

    private function canvasColor()
    {
        return is_null($this->command->background()) ? 'none' : '#' . $this->command->background();
    }
}
