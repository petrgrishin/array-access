<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

class MainTest extends PHPUnit_Framework_TestCase {
    public function testInstance() {
        $instance = new \Util\ArrayAccess();
        $this->assertInstanceOf(\Util\ArrayAccess::className(), $instance);
    }

    public function testCreateInstanceByStaticMethod() {
        $instance = \Util\ArrayAccess::create();
        $this->assertInstanceOf(\Util\ArrayAccess::className(), $instance);
    }
}
