<?php

class Home_Controller {
    public function index() {
    }
    public function number() {
        View::Set('number', URL::$Route['number']);
    }
}