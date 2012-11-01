<?php
namespace Barberry\Plugin\Imagemagick;

use Mockery as m;

class MonitoringTest extends \PHPUnit_Framework_TestCase
{

    private $testDirWritable;
    private $testDirNotWritable;

    protected function setUp()
    {
        $this->testDirNotWritable = realpath(__DIR__ . '/../tmp') . '/testdir-notwritable/';
        $this->testDirWritable = realpath(__DIR__ . '/../tmp') . '/testdir-writable/';
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
        $monitor = new Monitor($this->testDirNotWritable);
        $this->assertEquals(array('ERROR: Plugin imagemagick temporary directory is not writeable.'), $monitor->reportMalfunction());
    }

    public function testReturnOkIfDirectoryIsWritable()
    {
        $monitor = new Monitor($this->testDirWritable);
        $this->assertEquals(array(), $monitor->reportMalfunction());
    }

    public function testReturnErrorIfNoUnixCommandFound()
    {
        $monitor = $this->getMock('Barberry\Plugin\Imagemagick\Monitor', array('dependencies'), array($this->testDirWritable));
        $monitor->expects($this->any())->method('dependencies')->will($this->returnValue(array('no-command'=>'Please install no-command')));
        $this->assertEquals(array("MISSING - Please install no-command\n\n"), $monitor->reportUnmetDependencies());
    }
}
