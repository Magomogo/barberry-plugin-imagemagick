<?php
namespace Barberry\Plugin\Imagemagick;

use Barberry\Plugin\InterfaceCommand;

class Command implements InterfaceCommand
{
    const MAX_WIDTH = 800;
    const MAX_HEIGHT = 600;

    private $width;
    private $height;
    private $noUpscale;
    private $background;
    private $canvasWidth;
    private $canvasHeight;
    private $quality;
    private $colorspace;
    private $trimColor;
    private $trimFuzz;
    private $stripColorProfiles;

    /**
     * @param string $commandString
     * @return self
     */
    public function configure($commandString)
    {
        $params = explode("_", $commandString);
        foreach ($params as $val) {
            if (preg_match("@^([\d]*)x([\d]*)(noUpscale|)@", $val, $regs)) {
                $this->width = strlen($regs[1]) ? (int)$regs[1] : null;
                $this->height = strlen($regs[2]) ? (int)$regs[2] : null;
                $this->noUpscale = strlen($regs[3]) ? true : false;
            }
            if (preg_match("@bg([0-F]{3,6})@", $val, $regs)) {
                $this->background = $regs[1];
            }
            if (preg_match("@canvas([\d]*)x([\d]*)@", $val, $regs)) {
                $this->canvasWidth = strlen($regs[1]) ? (int)$regs[1] : null;
                $this->canvasHeight = strlen($regs[2]) ? (int)$regs[2] : null;
            }
            if (preg_match("@quality([\d]*)@", $val, $regs)) {
                $this->quality = strlen($regs[1]) ? (int)$regs[1] : null;
            }
            if (preg_match("@colorspace(Gray|CMYK|sRGB|Transparent|RGB)@", $val, $regs)) {
                $this->colorspace = strlen($regs[1]) ? $regs[1] : null;
            }
            if (preg_match("@trim((?:[0-F]{3,6})?)x((?:\d{1,2})?)@", $val, $regs)) {
                $this->trimColor = $regs[1];
                $this->trimFuzz = $regs[2];
            }
            if (preg_match("@strip@", $val)) {
                $this->stripColorProfiles = true;
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

    public function noUpscale()
    {
        return $this->noUpscale === true;
    }

    public function stripColorProfiles()
    {
        return $this->stripColorProfiles === true;
    }

    public function background()
    {
        return $this->background;
    }

    public function canvasWidth()
    {
        return min($this->canvasWidth, self::MAX_WIDTH);
    }

    public function canvasHeight()
    {
        return min($this->canvasHeight, self::MAX_HEIGHT);
    }

    public function quality()
    {
        return $this->quality;
    }

    public function colorspace()
    {
        return $this->colorspace;
    }


    public function trimColor()
    {
        return $this->trimColor;
    }

    public function trimFuzz()
    {
        return $this->trimFuzz;
    }

    public function __toString()
    {
        $str = ($this->width || $this->height) ? strval($this->width . 'x' . $this->height) . ($this->noUpscale ? 'noUpscale' : '') : '';
        if ($this->background) {
            $str .= 'bg' . $this->background;
        }
        if ($this->canvasWidth || $this->canvasHeight) {
            $str .= 'canvas' . strval($this->canvasWidth . 'x' . $this->canvasHeight);
        }
        if ($this->quality) {
            $str .= 'quality' . $this->quality;
        }
        if ($this->colorspace) {
            $str .= 'colorspace' . $this->colorspace;
        }
        if (!is_null($this->trimColor) || !is_null($this->trimFuzz)) {
            $str .= 'trim' . strval($this->trimColor . 'x' . $this->trimFuzz);
        }
        if ($this->stripColorProfiles) {
            $str .= 'strip';
        }
        return $str;
    }
}
