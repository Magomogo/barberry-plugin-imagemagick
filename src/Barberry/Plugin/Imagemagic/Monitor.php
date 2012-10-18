<?php
namespace Barberry\Plugin\Imagemagic;
use Barberry\Plugin;

class Monitor implements Plugin\InterfaceMonitor
{
    public function __construct($tmpDir)
    {

    }

    public function reportUnmetDependencies()
    {
        return array();
    }

    public function reportMalfunction()
    {
        return array();
    }
}
