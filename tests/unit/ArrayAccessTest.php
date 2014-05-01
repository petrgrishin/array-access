<?php
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
        ),
    );

    public function testCreateInstance() {
        $instance = new \Util\ArrayAccess();
        $this->assertInstanceOf(\Util\ArrayAccess::className(), $instance);
    }

    public function testCreateInstanceByStaticMethod() {
        $instance = \Util\ArrayAccess::create();
        $this->assertInstanceOf(\Util\ArrayAccess::className(), $instance);
    }

    public function testCreateEmptyArray() {
        $instance = \Util\ArrayAccess::create();
        $this->assertEquals(array(), $instance->toArray());
    }

    public function testReturnArray() {
        $instance = \Util\ArrayAccess::create($this->testArray);
        $this->assertEquals($this->testArray, $instance->toArray());
    }

    public function testGetValueByPath() {
        $instance = \Util\ArrayAccess::create($this->testArray);
        $this->assertEquals($this->testArray['key3']['key31'], $instance->getValue('key3.key31'));
    }

    public function testReturnValueBySimpleKey() {
        $instance = \Util\ArrayAccess::create($this->testArray);
        $this->assertEquals($this->testArray['key1'], $instance->getValue('key1'));
    }

    /**
     * @expectedException \Exception
     */
    public function testNotFoundValue() {
        $instance = \Util\ArrayAccess::create($this->testArray);
        $instance->getValue('notExistKey');
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidPathNotExistKey() {
        $instance = \Util\ArrayAccess::create($this->testArray);
        $instance->getValue('key3.notExistKey');
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidPathNotArray() {
        $instance = \Util\ArrayAccess::create($this->testArray);
        $instance->getValue('key3.key32.notExistKey');
    }
}
