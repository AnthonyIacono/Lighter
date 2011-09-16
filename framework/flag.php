<?php

class Flag {
    private function __construct() {
        // *** Do not touch me *** //
    }
    private static $Flags = array();
    public static function TossFlag($flag) {
        if(self::CheckFlag($flag)) {
            return;
        }
        Flag::$Flags[] = $flag;
    }
    public static function CheckFlag($flag) {
        return in_array($flag, Flag::$Flags);
    }
}