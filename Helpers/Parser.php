<?php

namespace Helpers;

class Parser
{
    /**
     * @param $filename
     * @return array|bool
     * Reads a CSV file and returns the csv in form of an array
     */
    public function parseCsv(string $filename)
    {
        $file = fopen($filename, 'r');
        if (!$file) {
            return false;
        }

        $csvArray = [];
        $i=0;
        while (($data = fgetcsv($file)) !== false) {
            //Filter invalid data
            if (count($data) < 3) {
                continue;
            }
            $csvArray[] = $data;
        }
        fclose($file);

        //remove first line
        array_shift($csvArray);
        return $csvArray;
    }
}
