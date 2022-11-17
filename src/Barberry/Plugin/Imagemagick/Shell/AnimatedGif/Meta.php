<?php

namespace Barberry\Plugin\Imagemagick\Shell\AnimatedGif;

use Barberry\Plugin\Imagemagick\Shell\AnimatedGif\Meta\Frame;

class Meta
{

    private $framesCount = 0;

    private $frames = [];

    /**
     * @param $identifyMatches
     */
    public function __construct($identifyMatches)
    {
        $this->framesCount = count($identifyMatches);

        foreach ($identifyMatches[1] as $i => $frameNum) {
            $this->frames[$frameNum] = new Frame(
                $identifyMatches[2][$i],
                $identifyMatches[2][$i],
                $identifyMatches[3][$i],
                $identifyMatches[4][$i],
                $identifyMatches[5][$i]
            );
        }
    }

    /**
     * @return int
     */
    public function getFramesCount()
    {
        return $this->framesCount;
    }

    /**
     * @param $frameIndex
     * @return Frame
     */
    public function getFrame($frameIndex)
    {
        if (!array_key_exists($frameIndex, $this->frames)) {
            throw new \RuntimeException(sprintf('Frame %s not found', $frameIndex));
        }

        return $this->frames[$frameIndex];
    }
}
