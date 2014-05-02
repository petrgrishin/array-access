<?php
use \PetrGrishin\ArrayAccess\ArrayAccess;

/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

class ArrayAccessTest extends PHPUnit_Framework_TestCase {
    private $testArray = array(
        0 => 'value0',
        'key1' => 'value1',
        'key2' => 2,
        'key3' => array(
            'key31' => array(1, 2, 3),
            'key32' => 'value32',
            'keyNullValue' => null,
        ),
        'keyNullValue' => null,
    );

    public function testCreateInstance() {
        $instance = new ArrayAccess();
        $this->assertInstanceOf(ArrayAccess::className(), $instance);
    }

    public function testCreateInstanceByStaticMethod() {
        $instance = ArrayAccess::create();
        $this->assertInstanceOf(ArrayAccess::className(), $instance);
    }

    public function testCreateEmptyArray() {
        $instance = ArrayAccess::create();
        $this->assertEquals(array(), $instance->toArray());
    }

    public function testReturnArray() {
        $instance = ArrayAccess::create($this->testArray);
        $this->assertEquals($this->testArray, $instance->toArray());
    }

    public function testGetValueByPath() {
        $instance = ArrayAccess::create($this->testArray);
        $this->assertEquals($this->testArray['key3']['key31'], $instance->getValue('key3.key31'));
    }

    public function testReturnValueBySimpleKey() {
        $instance = ArrayAccess::create($this->testArray);
        $this->assertEquals($this->testArray['key1'], $instance->getValue('key1'));
    }

    public function testReturnNullValueBySimpleKey() {
        $instance = ArrayAccess::create($this->testArray);
        $this->assertNull($instance->getValue('keyNullValue'));
        $this->assertNull($instance->getValue('key3.keyNullValue'));
    }

    /**
     * @expectedException \Exception
     */
    public function testNotFoundValue() {
        $instance = ArrayAccess::create($this->testArray);
        $instance->getValue('notExistKey');
    }

    /**
     * @depends testNotFoundValue
     */
    public function testGetDefaultValue() {
        $instance = ArrayAccess::create($this->testArray);
        $this->assertFalse($instance->getValue('notExistKey', false));
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidPathNotExistKey() {
        $instance = ArrayAccess::create($this->testArray);
        $instance->getValue('key3.notExistKey');
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidPathNotArray() {
        $instance = ArrayAccess::create($this->testArray);
        $instance->getValue('key3.key32.notExistKey');
    }
}
