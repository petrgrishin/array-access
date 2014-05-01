<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace Util;


class ArrayAccess {
    /** @var array */
    private $data;
    /** @var string */
    private $pathDelimiter;

    public static function className() {
        return get_called_class();
    }

    public static function create(array $data = null, $pathDelimiter = null) {
        return new static($data, $pathDelimiter);
    }

    public function __construct(array $data = null, $pathDelimiter = null) {
        $this->data = $data ?: array();
        $this->pathDelimiter = $pathDelimiter ?: '.';
    }

    public function toArray() {
        return $this->data;
    }
}
