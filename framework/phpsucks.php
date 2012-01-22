<?php

class PhpSucks {
    public static function ToIndexBasedArray($input) {
        $output = array();
        if(!is_array($input) && !is_object($input)) {
            return $output;
        }

        foreach($input as $i) {
            $output[] = $i;
        }
        return $output;
    }
}