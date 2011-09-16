<?php

class URLLib {
    private function __construct() {

    }

    public static function Validate($input) {
        $input = StrLib::Trim($input);
        if(strlen($input) < 8) {
            $input = 'http://' . $input;
        }
        $host_check = StrLib::Lower(substr($input, 0, 8));
        $prepend = ($host_check == 'https://' or substr($host_check, 0, 7) == 'http://') ? '' : 'http://';
        $input = $prepend . $input;
        $pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/';
        return preg_match($pattern, $input) ? $input : false;
    }
}