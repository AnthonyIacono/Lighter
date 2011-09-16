<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Lib::Import('logic');

class Request {
    private static $HTTPCodes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        510 => 'Not Extended'
    );
    private function __construct() {
        // *** Do not touch me *** //
    }
    public static function HTTPCode($code) {
        header('HTTP/1.1 ' . $code . ' ' . Request::$HTTPCodes[$code]);
        View::$Layout = '';
        View::Draw('');
    }
    public static function URI() {
        return $_SERVER['REQUEST_URI'];
    }
    public static function IsPost() {
        $is_post = (strtolower($_SERVER['REQUEST_METHOD']) == 'post');
        $post_data = Request::PostData();
        return $is_post and !(isset($post_data['is_delete']) && Logic::ToBool($post_data['is_delete']));
    }
    public static function IsDelete() {
        $is_post = (strtolower($_SERVER['REQUEST_METHOD']) == 'post');
        if(!$is_post) {
            return false;
        }
        $post_data = Request::PostData();
        if(isset($post_data['is_delete']) && Logic::ToBool($post_data['is_delete'])) {
            return true;
        }
        return false;
    }
    public static function IsPut() {
        return (strtolower($_SERVER['REQUEST_METHOD']) == 'put');
    }
    public static function IsGet() {
        return (strtolower($_SERVER['REQUEST_METHOD']) == 'get');
    }
    public static function PostData() {
        return $_POST;
    }
    public static function Redirect($where) {
        header('Location: ' . $where);
        View::$Layout = '';
        View::Draw('');
    }
}
