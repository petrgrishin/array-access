<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace Util;


class ArrayAccess {
    public static function className() {
        return get_called_class();
    }

    public static function create() {
        return new static();
    }
}
