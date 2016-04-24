<?php
namespace Phossa\Config\Loader;

/**
 * XmlLoader test case.
 */
class XmlLoaderTest extends \PHPUnit_Framework_TestCase
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
     * @covers Phossa\Config\Loader\XmlLoader::load()
     */
    public function testLoad1()
    {
        $this->assertEquals(
            ['test' => 'wow'], XmlLoader::load(__DIR__ . '/conf/config_good.xml')
        );
    }

    /**
     * No file found
     *
     * @covers Phossa\Config\Loader\XmlLoader::load()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "Load config file .*error"
     * @expectedExceptionCode Phossa\Config\Message\Message::CONFIG_LOAD_ERROR
     */
    public function testLoad2()
    {
        $this->assertEquals(
            ['test' => 'wow'], XmlLoader::load(__DIR__ . '/conf/nosuchfile.xml')
        );
    }

    /**
     * bad format
     *
     * @covers Phossa\Config\Loader\XmlLoader::load()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "Opening"
     * @expectedExceptionCode Phossa\Config\Message\Message::CONFIG_FORMAT_ERROR
     */
    public function testLoad3()
    {
        $this->assertEquals(
            ['test' => 'wow'], XmlLoader::load(__DIR__ . '/conf/config_bad.xml')
        );
    }
}
