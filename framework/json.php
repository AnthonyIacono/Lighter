<?php

class Json {
    private function __construct() {
        
    }
    public static function Encode($input) {
        return json_encode($input);
    }
    public static function Decode($input) {
        return json_decode($input);
    }
}