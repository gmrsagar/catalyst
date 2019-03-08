<?php

class Database {

    private $host;
    private $user;
    private $password;
    private $db_name;

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
        $sub_sql = '';
        foreach($array as $key => $val){
            //skip wrong data
            if(!is_array($val)) continue;

            $vals = array_reduce($val, function($carry, $item){
                //filter
                $item = filter_var($item, FILTER_SANITIZE_STRING);
                return $carry."'".$item."','";
            });
            $vals = substr($vals, 0 , -1);
            $sub_sql .= "($vals)".',';
        }
        $sql = $sql.substr_replace($sub_sql, ";", -1);
        return $sql;
    }
}