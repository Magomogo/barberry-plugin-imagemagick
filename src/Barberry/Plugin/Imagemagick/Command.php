<?php
namespace Barberry\Plugin\Imagemagick;

use Barberry\Plugin\InterfaceCommand;

class Command implements InterfaceCommand
{

    const MAX_WIDTH = 800;
    const MAX_HEIGHT = 600;

    private $width;
    private $height;

    /**
     * @param string $commandString
     * @return Command
     */
    public function configure($commandString)
    {
        $params = explode("_", $commandString);
        foreach ($params as $val) {
            if (preg_match("@^([\d]*)x([\d]*)$@", $val, $regs)) {
                $this->width = strlen($regs[1]) ? (int)$regs[1] : null;
                $this->height = strlen($regs[2]) ? (int)$regs[2] : null;
            }
        }
        return $this;
    }

    public function conforms($commandString)
    {
        return strval($this) === $commandString;
    }

    public function width()
    {
        return min($this->width, self::MAX_WIDTH);
    }

    public function height()
    {
        return min($this->height, self::MAX_HEIGHT);
    }

    public function __toString()
    {
        return ($this->width || $this->height) ? strval($this->width . 'x' . $this->height) : '';
    }
}
