<?php

class Logic {
    private function __construct() {

    }
    public static function ToBool($value) {
        if(is_numeric($value)) {
            return $value != 0;
        }
        $value = trim(strtolower((string)$value));
        return ($value != 'false' and $value != 'nil' and $value != 'null' and $value != '' and $value != 'undefined');
    }
    public static function BoolToBit($value) {
        return self::ToBool($value) ? 1 : 0;
    }
}