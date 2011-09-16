<?php

include_once('libs.php');

Lib::Import(array('config', 'session', 'router', 'view', 'routes', 'url', 'flag', 'http', 'mysql', 'log', 'html'));
Config::Import(array('application', 'database'));

class Application {
    private static $Initialized = false;

    private function __construct() {
        // *** Do not touch me *** //
    }

    public static function Initialize() {
        if(self::$Initialized) {
            return;
        }
        self::$Initialized = true;
        if(isset(Config::$Configs['application']['debug']) and Config::$Configs['application']['debug']) {
            error_reporting(E_ERROR);
        }
        else {
            error_reporting(E_ALL);
        }
        Session::Start();
        if(Database::Connect(Config::$Configs['database']['host'], Config::$Configs['database']['user'], Config::$Configs['database']['pass'], Config::$Configs['database']['db']) === false) {
            Request::HTTPCode(500);
            return;
        }
    }
    public static function Run() {
        $request_uri = Request::URI();
        URL::$Route = Router::Route($request_uri);

        if(count(URL::$Route) == 0) {
            Request::HTTPCode(404);
            return;
        }

        Lib::Import('controllers/'.URL::$Route['controller']);

        if(!class_exists(URL::$Route['controller'] . '_controller')) {
            Request::HTTPCode(404);
            return;
        }

        $className = URL::$Route['controller'] . '_controller';
        $controllerObject = new $className;

        if(URL::$Route['action'][0] == '_') {
            Request::HTTPCode(404);
            return;
        }

        if(!method_exists($controllerObject, URL::$Route['action'])) {
            Request::HTTPCode(404);
            return;
        }

        $stopAction = false;

        if(method_exists($controllerObject, '__before')) {
            if($controllerObject->__before() === false) {
                $stopAction = true;
            }
        }

        if(!$stopAction) {
            $controllerObject->{URL::$Route['action']}();
            View::Draw(URL::$Route['controller'] . '/' . URL::$Route['action']);
        }
    }
}

Application::Initialize();