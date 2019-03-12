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
    public static function getInstance(String $user, String $pass, String $host)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new Database($user, $pass, $host);
        }
        return self::$instance;
    }

    private function __construct(String $user = '', String $password = '', String $host = '')
    {
        if ($user=='' || $password=='' || $host=='') {
            $env = Dotenv::create(__DIR__.'/../');
            $env->load();

            $this->host = $_ENV['MSQL_HOST'];
            $this->user = $_ENV['MSQL_USER'];
            $this->password = $_ENV['MSQL_PASS'];
        } else {
            $this->host = $host;
            $this->user = $user;
            $this->password = $password;
        }

        $this->link = mysqli_connect(
            $this->host,
            $this->user,
            $this->password,
            $this->db_name
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
    public function buildInsertQuery(array $columns, array $array, string $table, Database $db)
    {
        $cols = array_reduce($columns, function ($carry, $item) {
            return $carry.'`'.$item.'`,';
        });
        //check data
        if (!isset($array)||empty($array)) {
            return false;
        }
        //remove last comma
        $cols = substr($cols, 0, -1);        
        $sql = $db->getLink()->prepare("INSERT INTO "."`$table`"." ($cols) VALUES (?, ?, ?)");
        $result = false;
        $sql->bind_param('sss', $array[0], $array[1], $array[2]);
        if ($sql->execute()) {
            $result = true;
        } else {
            throw new \Exception($sql->error);
        }
        $sql->close();
        return $result;
    }
}
