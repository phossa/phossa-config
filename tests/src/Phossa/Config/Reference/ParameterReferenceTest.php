<?php

namespace Phossa\Config\Reference;

/**
 * ParameterReference test case.
 */
class ParameterReferenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var ParameterReference
     */
    private $object;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new ParameterReference();

        // data to use
        $this->data = [
            'test1' => '${wow1}',
            'test2' => [
                'test3' => 'wow3'
            ],
            'wow1'  => '${test2.test3}',
            'wow3'  => 'xxx'
        ];
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
     * get protected method to test
     *
     * @param  string $className
     * @param  string $methodName
     * @return \ReflectionMethod
     * @access protected
     */
    protected function getMethod($methodName) {
        $class = new \ReflectionClass('\\Phossa\\Config\\Reference\\ParameterReference');
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @covers Phossa\Config\Reference\ParameterReference::hasReference()
     */
    public function testHasReference()
    {
        return;
        // no ref
        $str1 = 'dd';
        $this->assertFalse($this->object->hasReference($str1));

        // has ref
        $str2 = '${test}dd';
        $this->assertEquals(
            ['${test}' => 'test'],
            $this->object->hasReference($str2)
        );

        // multiple refs
        $str3 = '${test1}dd${test2}';
        $this->assertEquals(
            ['${test1}' => 'test1', '${test2}' => 'test2'],
            $this->object->hasReference($str3)
        );

        // recursive refs
        $str3 = '${${test1}}dd';
        $this->assertEquals(
            ['${test1}' => 'test1'],
            $this->object->hasReference($str3)
        );
    }

    /**
     * @covers Phossa\Config\Reference\ParameterReference::deReference()
     */
    public function testDeReference1()
    {
        // no ref
        $str1 = 'dd';
        $this->assertEquals($str1, $this->object->deReference($str1));

        // set data
        $this->object->setReferencePool($this->data);

        // has ref
        $str2 = '${test1}dd';
        $this->assertEquals('wow3dd', $this->object->deReference($str2));

        // multiple refs, leveled refs
        $str3 = '${test1}dd${test2.test3}';
        $this->assertEquals('wow3ddwow3', $this->object->deReference($str3));

        // recursive refs
        $str4 = '${${test1}}dd';
        $this->assertEquals('xxxdd', $this->object->deReference($str4));

        // reference to array
        $str5 = '${test2}';
        $this->assertEquals(['test3' => 'wow3'], $this->object->deReference($str5));
    }

    /**
     * reference unknown
     *
     * @covers Phossa\Config\Reference\ParameterReference::deReference()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "name .* unknown"
     * @expectedExceptionCode Phossa\Config\Message\Message::CONFIG_REF_UNKNOWN
     */
    public function testDeReference2()
    {
        // unknown
        $str1 = '${test1}xx';
        $this->assertEquals($str1, $this->object->deReference($str1));
    }

    /**
     * malformed reference
     *
     * @covers Phossa\Config\Reference\ParameterReference::deReference()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "Malformed"
     * @expectedExceptionCode Phossa\Config\Message\Message::CONFIG_REF_MALFORM
     */
    public function testDeReference3()
    {
        // set data
        $this->object->setReferencePool($this->data);

        // dereferenced array into a string
        $str1 = '${test2}xx';
        $this->assertEquals($str1, $this->object->deReference($str1));
    }

    /**
     * reference loop
     *
     * @covers Phossa\Config\Reference\ParameterReference::deReference()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "loop"
     * @expectedExceptionCode Phossa\Config\Message\Message::CONFIG_REF_LOOP
     */
    public function testDeReference4()
    {
        // set data
        $data = [
            'testX' => '${testY}',
            'testY' => '${testX}'
        ];
        $this->object->setReferencePool($data);

        // loop found
        $str = '${testX}';
        $this->object->deReference($str);
    }

    /**
     * @covers Phossa\Config\Reference\ParameterReference::deReferenceArray()
     */
    public function testDeReferenceArray1()
    {
        // set data
        $this->object->deReferenceArray($this->data);

        $this->assertEquals('wow3', $this->data['test1']);
    }

    /**
     * @covers Phossa\Config\Reference\ParameterReference::getReferenceValue()
     */
    public function testGetReferenceValue1()
    {
        $this->object->setReferencePool($this->data, true);

        $this->assertEquals('xxx',  $this->object->getReferenceValue('wow3'));
        $this->assertEquals('wow3', $this->object->getReferenceValue('wow1'));
        $this->assertEquals('wow3', $this->object->getReferenceValue('test1'));
    }


    /**
     * test super globals
     *
     * @covers Phossa\Config\Reference\ParameterReference::getReferenceValue()
     */
    public function testGetReferenceValue2()
    {
        $_SERVER['TEST'] = 'bingo';

        $this->assertEquals('bingo',  $this->object->getReferenceValue('_SERVER.TEST'));

        unset($_SERVER['TEST']);
    }

    /**
     * get super globals failed
     *
     * @covers Phossa\Config\Reference\ParameterReference::getReferenceValue()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "name .* unknown"
     * @expectedExceptionCode Phossa\Config\Message\Message::CONFIG_REF_UNKNOWN
     */
    public function testGetReferenceValue3()
    {
        $this->assertEquals('bingo',  $this->object->getReferenceValue('_SERVER.TEST'));
    }
}

