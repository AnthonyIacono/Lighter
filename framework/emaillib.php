<?php

class EmailLib {
    private function __construct() {

    }
    public static function Valid($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}