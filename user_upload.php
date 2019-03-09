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
    $error = 'Could not Insert the row '.$array.' '.$error.PHP_EOL;
    if (file_put_contents('error.log', $error, FILE_APPEND)) {
        return true;
    }
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
                echo 'Usage :: php user_upload.php [OPTIONS]'.PHP_EOL."\n";
                echo 'The following options are available'.PHP_EOL;
                echo '  --file csv_file_name    parse the given csv file and insert into the user table'.PHP_EOL;
                echo '  --dry_run               used with the --file directive. parse the given file without inserting to the database'.PHP_EOL;
                echo '  --create_table          create the MySQL user table'.PHP_EOL;
                echo '  -u MySQL_username       user for accessing MySQL'.PHP_EOL;
                echo '  -p MySQL_password       password to use while connecting to MySQL'.PHP_EOL;
                echo '  --help                  display this help screen'.PHP_EOL;
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
                
                //file parsing code
                $parser = Parser::getInstance();
                $files = $parser->parseCsv($filename);

                foreach ($files as $file) {
                    //Validate email address
                    if( !(filter_var($file[2], FILTER_VALIDATE_EMAIL)) ){
                        $error = 'Invalid Email, Skipping Record';
                        $log = csvErrorLogger($file, $error);
                        if($log) {
                            echo $error.PHP_EOL;
                        }
                        continue;  
                    }

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
                            echo mysqli_error($db).PHP_EOL;
                        }
                    }
                } 
            break;

            default:
                echo "i dont know  this";
        }
    }
    else {
        echo "invalid parameter";
    }
