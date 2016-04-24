<?php
namespace Phossa\Config\Reference;

require_once __DIR__ .'/Tree.php';

/**
 * TreeTrait test case.
 */
class TreeTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Tree
     */
    private $object;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        // TODO Auto-generated TreeTraitTest::setUp()

        $this->object = new Tree(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated TreeTraitTest::tearDown()
        $this->object = null;

        parent::tearDown();
    }

    /**
     * get protected method to test
     *
     * @param  string $className
     * @param  string $methodName
     * @return \ReflectionMethod
     * @access protected
     */
    protected function getMethod($methodName) {
        $class = new \ReflectionClass('\\Phossa\\Config\\Reference\\Tree');
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @covers Phossa\Config\Tree\TreeTrait::getFirstField()
     */
    public function testGetFirstField()
    {
        $method = $this->getMethod('getFirstField');

        $this->assertEquals(
            'test',
            $method->invokeArgs($this->object, ['test'])
        );

        $this->assertEquals(
            'bingo',
            $method->invokeArgs($this->object, ['bingo.test'])
        );

        $this->assertEquals(
            '',
            $method->invokeArgs($this->object, [''])
        );
    }

    /**
     * @covers Phossa\Config\Tree\TreeTrait::searchTree()
     */
    public function testSearchTree()
    {
        $method = $this->getMethod('searchTree');

        $tree = [
            'L1' => 'b1',
            'N1' => [
                'N2' => 'b2'
            ],
            'X1' => [
                'X2' => [
                    'X3' => 'b3'
                ]
            ]
        ];

        // search N1.N2
        $this->assertEquals(
            'b2',
            $method->invokeArgs($this->object, [ 'N1.N2', &$tree, false ])
        );

        // fail N1.X1
        $this->assertEquals(
            null,
            $method->invokeArgs($this->object, [ 'N1.X1', &$tree, false ])
        );

        // create X1.Y2.Z3
        $method->invokeArgs($this->object, [ 'X1.Y2.Z3', &$tree, true ]);
        $this->assertEquals(
            [], $tree['X1']['Y2']['Z3']
        );
    }

    /**
     * @covers Phossa\Config\Tree\TreeTrait::fixValue()
     */
    public function testFixValue()
    {
        $method = $this->getMethod('fixValue');

        // [ 'db.dsn' => 'xxx' ] to ['db' => [ 'dsn' => 'xxx' ]]
        $this->assertEquals(
            ['db' => [ 'dsn' => 'xxx' ]],
            $method->invokeArgs(
                $this->object,
                [[ 'db.dsn' => 'xxx' ]]
            )
        );

        // [ 'db.auth.host' => 'localhost', 'db.auth.user' => 'phossa' ]
        $this->assertEquals(
            ['db' => [ 'auth' => [ 'host' => 'localhost', 'user' => 'phossa']]],
            $method->invokeArgs(
                $this->object,
                [[ 'db.auth.host' => 'localhost', 'db.auth.user' => 'phossa' ]]
            )
        );
    }
}
