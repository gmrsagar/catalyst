<?php

namespace Database;

use Dotenv\Dotenv;


class Database
{
    private $host;
    private $user;
    private $password;
    private $db_name = 'catalyst';

    //db connection
    private $link;

    private static $instance;

    /**
     * @return DB
     * singleton
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $env = Dotenv::create(__DIR__.'/../');
        $env->load();

        $this->host = $_ENV['MSQL_HOST'];
        $this->user = $_ENV['MSQL_USER'];
        $this->password = $_ENV['MSQL_PASS'];

        $this->link = mysqli_connect(
            $this->host, $this->user, $this->password, $this->db_name
        );
    }

    //refuse clone
    private function __clone()
    {

    }

    //get db connection
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param $columns
     * @param $array
     * @return bool|string
     */
    public function buildInsertQuery($columns, $array, $table)
    {
        $cols = array_reduce($columns, function($carry, $item) {
            return $carry.'`'.$item.'`,';
        });

        //remove last comma
        $cols = substr($cols, 0 , -1);
        
        $sql = "INSERT INTO "."`$table`"." ($cols) VALUES ";

        //check data
        if(!isset($array)||empty($array)) return false;

        //build sub sql
        $array  = array_reduce($array, function($carry, $item) {
            return $carry.'"'.$item.'",';
        });
        $array = substr($array, 0, -1);
        $sub_sql = ($array);
        $sql = $sql."(".$sub_sql.");";
        return $sql;
    }

}