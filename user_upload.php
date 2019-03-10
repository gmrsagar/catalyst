<?php

require_once __DIR__ . '/vendor/autoload.php';

use Database\Database;
use Helpers\Parser;

$original_cols = [
    'name',
    'surname',
    'email'
];

/**
 * @param $array, $error
 * @return bool
 */
function csvErrorLogger($array, $error)
{
    $array = array_reduce($array, function($carry, $item) {
        return $carry.'`'.$item.'`,';
    });
    $error = 'Could not Insert the row '.$array.' '.$error."\n";
    if (file_put_contents('error.log', $error, FILE_APPEND)) {
        return true;
    }
    return false;
}

function updateEnv($string, $flag)
{
    if ($flag=='u') {
        $pattern = "/MSQL_USER=\.*/";
        $string = "MSQL_USER="."\"$string\"";
    } elseif ($flag=='p') {
        $pattern = "/MSQL_PASS=/";
        $string = "MSQL_PASS="."\"$string\"";
    } else {
        $pattern = "/MSQL_HOST=\.*/";
        $string = "MSQL_HOST="."\"$string\"";
    }

    $file = file_get_contents('.env');
    $output = preg_replace(
        $pattern, $string, $file
    );
    file_put_contents('.env', $output);
    if($output) return true;
    return false;
}

// Check if command is specified
if (!isset($argv[1])) {
    echo 'Type --help for a list of commands';
    return;
}
//0th position is phpfile name
//1st position is arguments , we can ignore other for fix it to 2
    $argument1 = $argv[1];
    if (substr( $argument1, 0, 2 ) === "--") {
    // It starts with '--'
        $command = substr($argument1, 2);

        switch ($command) {
            case 'create_table':
                //get db connection
                $db = Database::getInstance()->getLink();
                
                $sql = 'CREATE TABLE `users` ( 
                    `id` INT NOT NULL AUTO_INCREMENT ,
                    `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
                    `surname` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
                    `email` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
                    PRIMARY KEY (`id`),
                    UNIQUE `UNIQUE` (`email`(255)))
                    ENGINE = InnoDB';
                $result = mysqli_query($db, $sql) or die(mysqli_error($db));
                echo "Table created successfully";
            break;

            case 'help':
                //print the list of commands
            break;

            case 'file':
                $dryRun = false;

                // Check if dry run
                if (isset($argv[3])) {
                    if ($argv[3] === '--dry_run') {
                        $dryRun = true;
                    } else {
                        echo 'Invalid command, type --help for a list of available commands';
                        return;
                    }
                }
                
                if (!$dryRun) {
                // get db instance
                $db = Database::getInstance()->getLink();
                }

                //fetch the filename
                $filename = $argv[2];
                $filename = $filename.'.csv';
                
                //file parsing
                $parser = Parser::getInstance();
                $files = $parser->parseCsv($filename);

                foreach ($files as $file) {
                    //Validate email address
                    if( !(filter_var($file[2], FILTER_VALIDATE_EMAIL)) ){
                        $error = 'Invalid Email, Skipping Record';
                        $log = csvErrorLogger($file, $error);
                        if($log) {
                            echo $error."\n";
                        }
                        continue;  
                    }

                    //don't proceed if dry_run is enabled
                    if ($dryRun) continue;

                    $normalizedArray = [];

                    // Normalize the user data
                    foreach ($file as $item) {
                        $item = strtolower($item);
                        $item = ucfirst($item);
                        $normalizedArray[] = $item;
                    }
                    $normalizedArray[2] = strtolower($normalizedArray[2]);

                     //insert to db
                    $sql = Database::getInstance()->buildInsertQuery(
                        $original_cols, $normalizedArray, 'users'
                    );
                    
                    if ($sql === false) {
                        die('Failed to build sql');
                    }

                    //Display success or failure message
                    $results = mysqli_query($db, $sql);
                    if ($results) {
                        echo 'Data Inserted Successfully'.PHP_EOL;
                    } else {
                        $log = csvErrorLogger($normalizedArray, mysqli_error($db));
                        if($log) {
                            echo mysqli_error($db)."\n";
                        }
                    }
                } 
            break;

            default:
                echo "i dont know  this";
        }
    } elseif (substr( $argument1, 0, 1 ) === "-") {
        $command = substr($argument1, 1);
        
        if (($command === 'u') || ($command === 'p') || ($command === 'h')) {
            if (isset($argv[2])) {
                $string = $argv[2];
                $updateVar = updateEnv($string, $command);
                if ($updateVar) { echo 'Updated Successfully'; }
            } else {
                return;
            }
        } else {
            echo 'invalid';
            return;
        }
    }
        
    //     switch ($command) {
    //         case 'u':
    //             $updateUser = updateEnv($user, 'u');
    //             if ($updateUser) { echo 'Username Updated'; }
    //         break;

    //         case 'p':
    //         break;

    //         case 'h':
    //         break;

    //         default:
    //         echo 'I don\'t know this';
    //     }

    // } else {
    //     echo "invalid parameter";
    // }
