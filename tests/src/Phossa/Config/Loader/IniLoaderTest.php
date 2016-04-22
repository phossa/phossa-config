<?php
namespace Phossa\Config\Loader;

/**
 * IniLoader test case.
 */
class IniLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * normal
     *
     * @covers Phossa\Config\Loader\IniLoader::load()
     */
    public function testLoad1()
    {
        $this->assertEquals(
            ['test' => 'wow'], IniLoader::load(__DIR__ . '/config_good.ini')
        );
    }

    /**
     * No file found
     *
     * @covers Phossa\Config\Loader\IniLoader::load()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "Load config file .*error"
     */
    public function testLoad2()
    {
        $this->assertEquals(
            ['test' => 'wow'], IniLoader::load(__DIR__ . '/nosuchfile.ini')
        );
    }

    /**
     * bad format
     *
     * @covers Phossa\Config\Loader\IniLoader::load()
     * @expectedException Phossa\Config\Exception\LogicException
     */
    public function testLoad3()
    {
        $this->assertEquals(
            ['test' => 'wow'], IniLoader::load(__DIR__ . '/config_bad.ini')
        );
    }
}

