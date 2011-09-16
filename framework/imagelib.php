<?php

class ImageLib {
    private function __construct() {

    }

    public static function Render($path) {
        if(!file_exists($path)) {
            return false;
        }
        header('Content-Type: image');
        flush();
        readfile($path);
        return true;
    }
}