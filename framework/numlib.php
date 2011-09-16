<?php

class NumLib {
    private function __construct() {

    }
    public static function Unsigned($x) {
        return max(0, (int)$x);
    }
}