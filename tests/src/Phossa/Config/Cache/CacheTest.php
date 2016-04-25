<?php
namespace Phossa\Config\Cache;
require_once 'src/Phossa/Config/Cache/Cache.php';

/**
 * Cache test case.
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cache
     */
    private $object;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new Cache(__DIR__);
        $cache = $this->object;
        $cache(__DIR__, 'php', 'production');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->object = null;
        parent::tearDown();
    }

    /**
     * @covers Phossa\Config\Env\Environment::save()
     * @covers Phossa\Config\Env\Environment::get()
     * @covers Phossa\Config\Env\Environment::clear()
     */
    public function testSave()
    {
        $data = [ 'db' => ['dsn' => 'bingo']];
        $this->object->save($data);
        $this->assertEquals($data, $this->object->get());

        $this->object->clear();
        $this->assertFalse($this->object->get());
    }
}

