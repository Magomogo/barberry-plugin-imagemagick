<?php

namespace Barberry\Plugin\Imagemagick\Shell\AnimatedGif\Meta;

class Frame
{
    private $format;

    private $size;

    private $offset;

    private $colorDepth;

    private $colorSpace;

    /**
     * @param $format
     * @param $size
     * @param $offset
     * @param $colorDepth
     * @param $colorSpace
     */
    public function __construct($format, $size, $offset, $colorDepth, $colorSpace)
    {
        $this->format = $format;
        $this->size = $size;
        $this->offset = $offset;
        $this->colorDepth = $colorDepth;
        $this->colorSpace = $colorSpace;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return mixed
     */
    public function getColorDepth()
    {
        return $this->colorDepth;
    }

    /**
     * @return mixed
     */
    public function getColorSpace()
    {
        return $this->colorSpace;
    }
}
