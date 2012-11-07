<?php
namespace Barberry\Plugin\Imagemagic;
use Barberry\Plugin;

class Monitor implements Plugin\InterfaceMonitor
{
    private $tempDirectory;

    private $dependencies = array(
        'convert' => 'Please install imagemagic (http://www.imagemagick.org)'
    );

    public function configure($tmpDir)
    {
        $this->tempDirectory = $tmpDir;
        return $this;
    }

    public function dependencies()
    {
        return $this->dependencies;
    }

    public function reportUnmetDependencies()
    {
        $errors = array();
        foreach ($this->dependencies() as $command => $message) {
            $answer = $this->reportUnixCommand($command, $message);
            if (!is_null($answer)) {
                $errors[] = $answer;
            }
        }
        return $errors;
    }

    public function reportMalfunction()
    {
        $answer = $this->reportWritableDirectory($this->tempDirectory);
        return (!is_null($answer)) ? array($answer) : array();
    }

//-------------------------------------------------------------------------

    private function reportWritableDirectory($directory)
    {
        return (!is_writeable($directory)) ? 'ERROR: Plugin imagemagic temporary directory is not writeable.' : null;
    }

    private function reportUnixCommand($command, $messageIfMissing)
    {
        return preg_match('/^\/\w+/', exec("which $command")) ? null : "MISSING - $messageIfMissing\n\n";
    }
}
