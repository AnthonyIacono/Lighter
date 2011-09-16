<?php

class Router {
    private function __construct() {

    }
    private static $Routes = array();
    public static function Add($pattern, $stack) {
        if(!self::ValidRoutePattern($pattern)) {
            return false;
        }
        $params = explode('/', $pattern);
        array_shift($params);
        $params_new = array();
        foreach($params as $param) {
            if($param == '') {
                continue;
            }
            $params_new[] = $param;
        }
        $params = $params_new;
        unset($params_new);
        self::$Routes[] = array('stack' => $stack, 'params' => $params);
    }
    public static function Route($uri) {
        if(strlen($uri) < 1) {
            $uri = '/';
        }

        while(strstr($uri, '//') !== false) {
            $uri = str_replace('//', '/', $uri);
        }

        $url = parse_url($uri);
        if(isset($url['path'])) {
            $uri = $url['path'];
        }

        if(isset($url['query'])) {
            parse_str($url['query'], $get_vars);
        }

        if($uri[strlen($uri)-1]=='/') {
            $uri = substr($uri, 0, strlen($uri) - 1);
        }

        $params = explode('/', $uri);
        array_shift($params);

        $new_params = array();
        foreach($params as $param) {
            if($param == '') {
                continue;
            }

            $new_params[] = $param;
        }

        $params = $new_params;
        unset($new_params);

        $param_count = count($params);
        $named_params = array();

        foreach(self::$Routes as $route) {

            if(count($route['params']) != $param_count) {
                continue;
            }

            $match = true;

            foreach($route['params'] as $index => $param) {
                if($param[0] == ':') {
                    $named_params[substr($param, 1)] = $params[$index];
                    continue;
                }
                else if($param == '*') {
                    continue;
                }
                else if($param == $params[$index]) {
                    continue;
                }

                $match = false;
            }

            if(!$match) {
                $named_params = array();
                continue;
            }

            if(isset($route['stack']['class']) and $route['stack']['class'] != '') {
                if(!class_exists($route['stack']['class'])) {
                    continue;
                }

                $routeClass = new $route['stack']['class'];
                $params_copy = $named_params;
                $result = $routeClass->parse($params_copy);

                if($result === false) {
                    continue;
                }

                $named_params = $params_copy;
            }
            $stack = array_merge($named_params, $route['stack']);
            break;
        }

        if(!isset($stack)) {
            $stack = array();

            if(count($params)==1) {
                $stack['controller'] = $params[0];
                $stack['action'] = 'index';
            }
            else if(count($params) != 0) {
                $stack['controller'] = $params[0];
                $stack['action'] = $params[1];
            }
        }

        if(isset($get_vars) and is_array($get_vars)) {
            $stack['url'] = $get_vars;
        }
        
        return $stack;
    }
    public static function ValidRoutePattern($pattern) {
        if(strstr($pattern, '//') !== false) {
            return false;
        }

        if(!is_string($pattern) or strlen($pattern) < 1) {
            return false;
        }

        if($pattern[0] != '/') {
            return false;
        }
        
        return true;
    }
}