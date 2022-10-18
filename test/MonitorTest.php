<?php
namespace Barberry\Plugin\Imagemagick;

class MonitoringTest extends \PHPUnit_Framework_TestCase
{
    private $testDirWritable;
    private $testDirNotWritable;

    protected function setUp()
    {
        $this->testDirNotWritable = '/tmp/testdir-notwritable/';
        $this->testDirWritable = '/tmp/testdir-writable/';
        @mkdir($this->testDirNotWritable, 0444, true);
        @mkdir($this->testDirWritable, 0777, true);
    }

    protected function tearDown()
    {
        exec("rm -rf " . $this->testDirNotWritable);
        exec("rm -rf " . $this->testDirWritable);
    }

    public function testReturnErrorIfDirectoryIsNotWritable()
    {
        $monitor = self::monitor($this->testDirNotWritable);
        $this->assertEquals(array('ERROR: Plugin imagemagick temporary directory is not writeable.'), $monitor->reportMalfunction());
    }

    public function testReturnOkIfDirectoryIsWritable()
    {
        $monitor = self::monitor($this->testDirWritable);
        $this->assertEquals(array(), $monitor->reportMalfunction());
    }

    public function testReturnErrorIfNoUnixCommandFound()
    {
        $monitor = $this->getMockBuilder('Barberry\Plugin\Imagemagick\Monitor')
            ->setMethods(['dependencies'])
            ->enableOriginalConstructor()
            ->getMock();

        $monitor->configure($this->testDirWritable);
        $monitor->expects($this->any())
            ->method('dependencies')
            ->will($this->returnValue(array('no-command'=>'Please install no-command')));

        $this->assertEquals(array("MISSING - Please install no-command\n\n"), $monitor->reportUnmetDependencies());
    }

    private static function monitor($tmpDir)
    {
        $monitor = new Monitor();
        return $monitor->configure($tmpDir);
    }
}
