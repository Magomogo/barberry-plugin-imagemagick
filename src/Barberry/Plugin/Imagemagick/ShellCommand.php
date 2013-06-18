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
        if ($this->command->width() || $this->command->height()) {
            $string .= ' -resize ' . $this->command->width() . 'x' . $this->command->height();
        }
        if (!is_null($this->command->background())) {
            $string .= ' -background "#' . $this->command->background() . '" -flatten';
        }
        if ($this->command->canvasWidth() || $this->command->canvasHeight()) {
            $string .=
                ' -size ' . $this->command->canvasWidth() . 'x' . $this->command->canvasHeight() .
                ' xc:#' . $this->canvasColor() . ' +swap -gravity center -composite';
        }
        return $string;
    }


    private function canvasColor()
    {
        return is_null($this->command->background()) ? '000000' : $this->command->background();
    }
}