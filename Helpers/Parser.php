<?php

namespace Helpers;

class Parser
{
    public static $instance;

    private function __construct()
    {

    }

    /**
     * @return Parser
     * Singleton
     */
    public static function getInstance()
    {
        if( !self::$instance instanceOf self) {
            self::$instance = new Parser();
        }
        return self::$instance;
    }

    //refuse clone
    private function __clone()
    {
        
    }

    /**
     * @param $filename
     * @return array|bool
     * Reads a CSV file and returns the csv in form of an array
     */
    public function parseCsv($filename)
    {
        $file = fopen($filename, 'r');
        if(!$file) return false;

        $csvArray = [];
        $i=0;
        while( ($data = fgetcsv($file)) !== false ) {

            //Filter invalid data
            if (count($data) < 3 ) continue;
            $csvArray[] = $data;
        }
        fclose($file);

        //remove first line
        array_shift($csvArray);
        return $csvArray;
    }

    
}