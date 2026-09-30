<?php
namespace Barberry\Plugin\Imagemagick;
use Barberry\Plugin;
use Barberry\ContentType;
use Barberry\Exception\ConversionNotPossible;

class Converter implements Plugin\InterfaceConverter
{
    const MAX_IN_MEMORY_SIZE = 2 * 1024 * 1024;

    /**
     * @var string
     */
    private $tempPath;

    /**
     * @var ContentType
     */
    private $targetContentType;

    public function configure(ContentType $targetContentType, $tempPath)
    {
        $this->tempPath = $tempPath;
        $this->targetContentType = $targetContentType;
        return $this;
    }

    public function convert($bin, Plugin\InterfaceCommand $command = null)
    {
        $shellCommand = new ShellCommand($command);
        if (strlen($bin) <= self::MAX_IN_MEMORY_SIZE) {
            return $this->convertInMemory($bin, $shellCommand);
        }

        $source = tempnam($this->tempPath, "imagemagick_");
        chmod($source, 0664);
        $destination = $source . '.' . $this->targetContentType->standardExtension();
        file_put_contents($source, $bin);
        $error = array();
        $exitCode = 0;
        exec('convert ' . escapeshellarg($source) . ' ' . strval($shellCommand) . ' ' . escapeshellarg($destination) . ' 2>&1', $error, $exitCode);
        if ($exitCode !== 0) {
            $reason = 'ImageMagick exited with code ' . $exitCode;
            if (!empty($error)) {
                $reason .= ': ' . implode("\n", $error);
            }
            throw new ConversionNotPossible($reason);
        }
        if (is_file($destination)) {
            $bin = file_get_contents($destination);
            unlink($destination);
        }
        unlink($source);

        return $bin;
    }

    private function convertInMemory($bin, ShellCommand $shellCommand)
    {
        $process = @proc_open(
            'convert - ' . strval($shellCommand) . ' ' . $this->targetContentType->standardExtension() . ':-',
            array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')),
            $pipes
        );
        if (!is_resource($process)) {
            throw new ConversionNotPossible('could not start ImageMagick');
        }

        foreach ($pipes as $pipe) {
            stream_set_blocking($pipe, false);
        }

        $offset = 0;
        $output = '';
        $error = '';
        $failure = null;
        while (!empty($pipes)) {
            if (isset($pipes[0]) && $offset === strlen($bin)) {
                fclose($pipes[0]);
                unset($pipes[0]);
            }

            $read = array();
            if (isset($pipes[1])) {
                $read[] = $pipes[1];
            }
            if (isset($pipes[2])) {
                $read[] = $pipes[2];
            }
            $write = isset($pipes[0]) ? array($pipes[0]) : array();
            if (empty($read) && empty($write)) {
                break;
            }
            $except = null;
            if (@stream_select($read, $write, $except, null) === false) {
                $failure = 'could not wait for ImageMagick pipes';
                proc_terminate($process);
                break;
            }

            foreach ($read as $pipe) {
                $data = @fread($pipe, 8192);
                if ($data === false) {
                    $failure = 'could not read ImageMagick output';
                    $index = isset($pipes[1]) && $pipe === $pipes[1] ? 1 : 2;
                    fclose($pipe);
                    unset($pipes[$index]);
                    continue;
                }
                if (isset($pipes[1]) && $pipe === $pipes[1]) {
                    $output .= $data;
                } else {
                    $error .= $data;
                }
                if ($data === '' && feof($pipe)) {
                    $index = isset($pipes[1]) && $pipe === $pipes[1] ? 1 : 2;
                    fclose($pipe);
                    unset($pipes[$index]);
                }
            }
            if (!empty($write)) {
                $written = @fwrite($pipes[0], substr($bin, $offset, 8192));
                if ($written === false || $written === 0) {
                    $failure = 'could not write image to ImageMagick';
                    fclose($pipes[0]);
                    unset($pipes[0]);
                } else {
                    $offset += $written;
                }
            }
        }

        foreach ($pipes as $pipe) {
            fclose($pipe);
        }
        $exitCode = proc_close($process);
        if ($failure !== null || $exitCode !== 0) {
            $reason = 'ImageMagick exited with code ' . $exitCode;
            if ($failure !== null) {
                $reason .= ' (' . $failure . ')';
            }
            if (trim($error) !== '') {
                $reason .= ': ' . trim($error);
            }
            throw new ConversionNotPossible($reason);
        }

        return $output;
    }
}
