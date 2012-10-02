<?php
namespace Barberry\Plugin\Imagemagic;
use Barberry\Plugin\InterfaceCommand;

class Command implements InterfaceCommand {

    const MAX_WIDTH = 800;
    const MAX_HEIGHT = 600;

    private $width;
    private $height;

    /**
     * @param string $commandString
     * @return Command
     */
    public function configure($commandString) {
        $params = explode("_",$commandString);
            foreach ($params as $val) {
            if (preg_match("@^([\d]*)x([\d]*)$@",$val,$regs)) {
                $this->width = strlen($regs[1]) ? min((int)$regs[1], self::MAX_WIDTH) : null;
                $this->height = strlen($regs[2]) ? min((int)$regs[2], self::MAX_HEIGHT) : null;
            }
        }
        return $this;
    }

    public function conforms($commandString) {
        return true;
    }

    public function width() {
        return $this->width;
    }

    public function height() {
        return $this->height;
    }

    public function __toString() {
        return strval($this->width.'x'.$this->height);
    }
}
