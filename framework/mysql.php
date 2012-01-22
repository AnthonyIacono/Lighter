<?php

/* $parameters
   $parameters['what'] => array(
 * 'id',
 * 'user_name',
 * );
 * $parameters['tables']*/


/*
 * 
 * ['where'] = array(array('id', '=', 24), 'or', array(array('blah', '>', 5), 'and', array('lala', '=', 1));
 */

class Database {
    private function __construct() {
        // *** Do not touch me *** //
    }
    const DefaultDB = 0;
    public static $Databases = array();
    public static function Connect($host, $user, $password, $start_db = '') {
        $link = mysqli_connect($host, $user, $password);
        if(!$link) {
            return false;
        }
        $index = count(self::$Databases);
        self::$Databases[] = array('link' => $link, 'info' => array(
            'host' => $host,
            'user' => $user,
            'password' => $password,
            'start_db' => $start_db
        ));
        if($start_db != '') {
            self::SelectDB($start_db, $index);
        }
        return $index;
    }
    public static function SelectDB($db_select, $db_index = self::DefaultDB) {
        if($db_index >= count(self::$Databases)) {
            return false;
        }
        return mysqli_select_db(self::$Databases[$db_index]['link'], $db_select);
    }
    public static function RealEscapeString($string, $db_index = 0) {
        if($db_index >= count(self::$Databases)) {
            return false;
        }
        return mysqli_real_escape_string(self::$Databases[$db_index]['link'], $string);
    }
    public static function Transaction($queries = array(), $db_index = 0) {
        if($db_index >= count(self::$Databases)) {
            return false;
        }
        self::Query("BEGIN", $db_index);
        foreach($queries as $query) {
            $query = trim($query);
            $len = strlen($query);
            if($len <= 0) {
                continue;
            }
            self::Query($query, $db_index);
        }
        self::Query("COMMIT", $db_index);
    }
    public static function Query($query, $db_index = 0) {
        $query .= preg_match('/;$/', $query) > 0 ? '' : ';';
        if($db_index >= count(self::$Databases)) {
            return false;
        }
        return mysqli_query(self::$Databases[$db_index]['link'], $query);
    }
    public static function Ping($db_index = 0) {
        if($db_index >= count(self::$Databases)) {
            return false;
        }
        if(!mysqli_ping(self::$Databases[$db_index]['link'])) {
            self::$Databases[$db_index]['link'] = mysqli_connect(self::$Databases[$db_index]['info']['host'], self::$Databases[$db_index]['info']['user'], self::$Databases[$db_index]['info']['password']);
            if(self::$Databases[$db_index]['info']['start_db'] != "") {
                self::SelectDB(self::$Databases[$db_index]['info']['start_db'], $db_index);
            }
        }
    }
    public static function FetchAssoc($result) {
        if($result === false) {
            return false;
        }
        return mysqli_fetch_assoc($result);
    }
    public static function FetchAll($result) {
        $items = array();
        while($row = self::FetchAssoc($result)) {
            $items[] = $row;
        }
        return $items;
    }
    public static function LastError($db_index = 0) {
        if($db_index >= count(self::$Databases)) {
            return false;
        }
        return mysqli_error(self::$Databases[$db_index]['link']);
    }
    public static function EscapeArray($array, $db_index = 0) {
        $output = array();
        foreach($array as $key => $value) {
            $output[$key] = self::RealEscapeString($value, $db_index);
        }
        return $output;
    }
    public static function Select($parameters = array(), $db_index = 0, &$error = null, &$query_output = null) {
        if($db_index >= count(self::$Databases)) {
            return false;
        }
        
        $default['what'] = array('*');
        $default['tables'] = array();
        $default['where'] = array();
        $default['order'] = '';
        $default['desc'] = false;
        $default['limit'] = '';
        $default['pagination'] = array();

        $parameters = array_merge($default, $parameters);

        if($parameters['limit'] != '' and count($parameters['pagination']) > 0) {
            if($error !== null) {
                $error = 'Cant use custom limit and pagination options';
            }
            return false;
        }
        if(!is_array($parameters['tables']) or count($parameters['tables'])==0) {
            if($error !== null) {
                $error = 'No tables selected';
            }
            return false;
        }

        if(!is_array($parameters['what']) or count($parameters['what'])==0) {
            if($error !== null) {
                $error = 'No columns selected';
            }
            return false;
        }

        $query = "SELECT ";
        $first = true;
        foreach($parameters['what'] as $column) {
            if($first) {
                $query .= self::RealEscapeString($column, $db_index);
            }
            else {
                $query .= ',' . self::RealEscapeString($column, $db_index);
            }
            $first = false;
        }

        $query .= ' FROM ';
        $first = true;
        foreach($parameters['tables'] as $table) {
            if($first) {
                $query .= self::RealEscapeString($table, $db_index);
            }
            else {
                $query .= ',' . self::RealEscapeString($table, $db_index);
            }
            $first = false;
        }

        $where_string = "";
        if(is_array($parameters['where']) and count($parameters['where'])>0) {
            $where_string = self::BuildWhereString($parameters['where']);
        }

        if($where_string != '') {
            $query .= ' WHERE '.$where_string;
        }

        if(is_string($parameters['order']) and $parameters['order'] != '') {
            $query .= ' ORDER BY ' . $parameters['order'];
        }

        if($parameters['limit'] != '') {
            $query .= ' LIMIT ' . $parameters['limit'];
        }
        if ($query_output !== null) {
            $query_output = $query;
        }
        $result = self::Query($query, $db_index);
        if(!$result) {
            $error = Database::LastError();
            return false;
        }
        $results = array();
        while($row = self::FetchAssoc($result)) {
            $results[] = $row;
        }
        return $results;
    }
    public static function BuildWhereString($where = array(), $db_index = self::DefaultDB) {
        if($db_index >= count(self::$Databases)) {
            return false;
        }
        $str = "";
        $other = false;
        $first = true;
        foreach($where as $condition) {
            if($other) {
                if(!is_string($condition)) {
                    return array('error'=>'Every other parameter in a where string must be a logical operator');
                }
                $condition = strtolower($condition);
                $allowed = array('and', 'or');
                if(!in_array($condition, $allowed)) {
                    return array('error'=>'Unacceptable logic operator: '.$condition);
                }
                $str .= ' '.$condition;
            }
            else {
                if(is_array($condition) and count($condition)==3 and isset($condition[0]) and !is_array($condition[0]) and isset($condition[1]) and is_string($condition[1]) and isset($condition[2])) {
                    $lstr = self::RealEscapeString($condition[0], $db_index);
                    $oper = (in_array($condition[1], array('=', '>', '<', '>=', '<=')))?$condition[1]:'';
                    if($oper == '') {
                        return array('error'=>'Bad comparison operator passed', 'operator'=>$condition[1]);
                    }
                    if(is_array($condition[2])) {
                        $rstr = self::RealEscapeString($condition[2]['reference'], $db_index);
                    }
                    else {
                        $rstr = '\''.self::RealEscapeString($condition[2], $db_index).'\'';
                    }
                    if($first) {
                        $str .= $lstr.$oper.$rstr;
                    }
                    else {
                        $str .= ' '.$lstr.$oper.$rstr;
                    }
                }
                else if(!is_array($condition)) {
                    return array('error'=>'Bad condition passed', 'condition'=>$condition);
                }
                else {
                    $tempstr = self::BuildWhereString($condition, $db_index);
                    if(is_array($tempstr)) {
                        return $tempstr;
                    }
                    if($tempstr != '') {
                        $str .= ' ('.$tempstr.')';
                    }
                }
            }
            $first = false;
            $other = !$other;
        }
        return $str;
    }
    public static function Delete($parameters = array(), $db_index = self::DefaultDB, &$query_output = null, &$error = null) {
        if($db_index >= count(self::$Databases)) {
            return false;
        }
        $default['table'] = '';
        $default['where'] = array();
        $parameters = array_merge($default, $parameters);
        if(!is_string($default['table']) or $default['table'] == '') {

            return false;
        }
        $query = 'DELETE FROM ';
        $first = true;
        foreach($parameters['tables'] as $table) {
            if($first) {
                $query .= self::RealEscapeString($table, $db_index);
            }
            else {
                $query .= ',' . self::RealEscapeString($table, $db_index);
            }
            $first = false;
        }
        $where_string = "";
        if(is_array($parameters['where']) and count($parameters['where'])>0) {
            $where_string = self::BuildWhereString($parameters['where']);
        }
        if($where_string != '') {
            $query .= ' WHERE '.$where_string;
        }
        if ($query_output !== null) {
            $query_output = $query;
        }
        $result = self::Query($query, $db_index);
        if(!$result) {
            return false;
        }
        $results = array();
        while($row = self::FetchAssoc($result)) {
            $results[] = $row;
        }
        return $results;
    }
    public static function InsertId($db_index = 0) {
        if($db_index >= count(self::$Databases)) {
            return false;
        }
        return mysqli_insert_id(self::$Databases[$db_index]['link']);
    }
    public static function NumRows($result) {
        return mysqli_num_rows($result);
    }
    public static function BuildQuery($format, $params, $db_index = 0) {
        return preg_replace_callback('/{{(P|E|U)[0-9]+}}/', function($matches) use($params, $db_index) {
            $index = (int)substr(substr($matches[0], 3), 0, -2);
            $param = isset($params[$index]) ? $params[$index] : '';
            if(!isset($matches[1]) || $matches[1] == 'P') {
                return $param;
            }

            return $matches[1] == 'U'
                ? NumLib::Unsigned($param)
                : Database::RealEscapeString($param, $db_index);
        }, $format);
    }
    public static function BuildAndQueryThenFetchAll($format, $params, $db_index = 0) {
        return Database::FetchAll(Database::Query(Database::BuildQuery($format, $params, $db_index), $db_index));
    }

    public static function BuildAndQuery($format, $params, $db_index = 0) {
        return Database::Query(Database::BuildQuery($format, $params, $db_index), $db_index);
    }
}