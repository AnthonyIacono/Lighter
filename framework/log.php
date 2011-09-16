<?php

class Log {
	public static $WarningFile = false;
	public static $ErrorFile = false;
	private function __construct() {
        // *** Do not touch me *** //
	}
	public static function Warning($log_text) {
		if(!self::$WarningFile) {
		    self::$WarningFile = fopen("system/logs/warnings.txt", "a+");
        }

        if(!self::$WarningFile) {
            return;
        }

        $date_str = date("m/d/Y g:ia");
	    fwrite(self::$WarningFile, "[".$date_str."] ".$log_text."\n");
	}
	function Error($log_text) {
		if(!self::$ErrorFile) {
		    self::$ErrorFile = fopen("system/logs/errors.txt", "a+");
        }

        if(!self::$ErrorFile) {
            return;
        }

        $date_str = date("m/d/Y g:ia");
	    fwrite(self::$ErrorFile, "[".$date_str."] ".$log_text."\n");
	}
}