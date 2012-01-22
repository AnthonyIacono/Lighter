<?php

class DateLib {
    private function __construct() {
        /** Don't touch **/
    }

    public static function Age($date, $now = false) {
        $date = StrLib::ParseDateTime($date);

        if($now === false) {
            $now = time();
        }

        $time_of_birth = mktime(0, 0, 0, $date['month'], $date['day'], $date['year']);

        $year_diff = date("Y", $now) - date("Y", $time_of_birth);
        $month_diff = date("m", $now) - date("m", $time_of_birth);
        $day_diff = date("d", $now) - date("d", $time_of_birth);

        if ($day_diff < 0 || $month_diff < 0)
            $year_diff--;

        return $year_diff;
    }
}