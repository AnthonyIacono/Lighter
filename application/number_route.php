<?php

class NumberRoute {
    public function parse(&$params) {
        return is_numeric($params['number']);
    }
}