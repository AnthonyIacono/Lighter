<?php

class Session {
    private function __construct() {
        // *** Do not touch me *** //
    }
    public static function Start() {
        @session_start();
    }
    public static function Destroy() {
        @session_destroy();
    }
}