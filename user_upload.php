<?php

require_once __DIR__ . '/vendor/autoload.php';

use Database\Database;
use Helpers\Parser;

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

//display help commands
if ($argv[1] === '--help') {
    echo 'Usage :: php user_upload.php [username] [password] [hostname] [OPTIONS]'.PHP_EOL."\n";
    echo 'The following options are available'.PHP_EOL;
    echo '  --file csv_file_name    parse the given csv file and insert into the user table'.PHP_EOL;
    echo '  --dry_run               used with the --file directive. parse the given file without inserting to the database'.PHP_EOL;
    echo '  --create_table          create the MySQL user table'.PHP_EOL;
    echo '  -u MySQL_username       user for accessing MySQL'.PHP_EOL;
    echo '  -p MySQL_password       password to use while connecting to MySQL'.PHP_EOL;
    echo '  --help                  display this help screen'.PHP_EOL;
    return;
}

/**
 * @param $keys
 * @param $arr
 * @return array
 * 
 * check if multiple keys are available within a given array
 */
function array_keys_exist(array $keys, array $arr)
{
    return array_diff_key(array_flip($keys), $arr);
}

/**
 * @param array
 * @return bool
 * 
 * check if proper options are set
 */
function mysqlAuthenticate(array $arr)
{
    //check if credentials are set
    $keysExist = array_keys_exist(['u', 'p', 'h'], $arr);

    if (count($keysExist)>0) {
        if (array_key_exists('file', $arr) && array_key_exists('dry_run', $arr)) {
            return true;
        }
        return false;
    }
    return true;
}

$shortOptions = "u:";
$shortOptions .= "p:";
$shortOptions .= "h:";
$longOptions = array(
    "file:",
    "create_table::",
    "dry_run"
);

$options = getopt($shortOptions, $longOptions);

$auth = mysqlAuthenticate($options);

if (!$auth) {
    //check if credentials are set
    $keysExist = array_keys_exist(['u', 'p', 'h'], $options);

    if (count($keysExist)>0) {
        foreach ($keysExist as $key => $value) {
            switch($key) {
                case 'u':
                echo 'please enter username'.PHP_EOL;
                break;

                case 'h':
                echo 'please enter hostname'.PHP_EOL;
                break;

                default:
                echo 'please enter password'.PHP_EOL;
                break;
            }
        }
        return;
    }
}

//check if dry run
$dryRun = true;

if (!(array_key_exists('file', $options) && array_key_exists('dry_run', $options))) {
//set mysql credentials
$mysqlUser = $options['u'];
$mysqlPass = $options['p'];
$mysqlHost = $options['h'];

//instantiate db
$db = Database::getInstance($mysqlUser, $mysqlPass, $mysqlHost);
$dbLink = $db->getLink();

$dryRun = false;
}

if (array_key_exists('file', $options) || array_key_exists('create_table', $options)) {

    foreach ($options as $key => $value) {
        
        switch ($key) {
            case 'create_table':
                $sql = 'CREATE TABLE IF NOT EXISTS `users` ( 
                    `id` INT NOT NULL AUTO_INCREMENT ,
                    `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
                    `surname` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
                    `email` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
                    PRIMARY KEY (`id`),
                    UNIQUE `UNIQUE` (`email`(255)))
                    ENGINE = InnoDB';
                $result = mysqli_query($dbLink, $sql) or die(mysqli_error($dbLink));
                echo "Table created successfully";
            break;

            case 'file':
            $filename = $value;
            $filename = $filename.'.csv';
            
            //file parsing
            $parser = Parser::getInstance();
            $files = $parser->parseCsv($filename);

            foreach ($files as $file) {
                //Validate email address
                if (!(filter_var($file[2], FILTER_VALIDATE_EMAIL))){
                    $error = 'Invalid Email, Skipping Record';
                    $log = csvErrorLogger($file, $error);
                    if ($log) {
                        echo $error.PHP_EOL;
                    }
                    continue;  
                }

                //don't proceed if dry_run is enabled
                if (!$dryRun) {

                    $normalizedArray = [];

                    // Normalize the user data
                    foreach ($file as $item) {
                        $item = strtolower($item);
                        $item = ucfirst($item);
                        $normalizedArray[] = $item;
                    }
                    $normalizedArray[2] = strtolower($normalizedArray[2]);

                    $original_cols = [
                        'name',
                        'surname',
                        'email'
                    ];

                    //insert to db
                    $sql = $db->buildInsertQuery(
                        $original_cols, $normalizedArray, 'users'
                    );
                    
                    if ($sql === false) {
                        die('Failed to build sql');
                    }

                    //Display success or failure message
                    $results = mysqli_query($dbLink, $sql);
                    if ($results) {
                        echo 'Data Inserted Successfully'.PHP_EOL;
                    } else {
                        $log = csvErrorLogger($normalizedArray, mysqli_error($dbLink));
                        if($log) {
                            echo mysqli_error($dbLink).PHP_EOL;
                        }
                    }
                }
            }
            break;

            default:
            break;
        }
    }
} else {
    echo 'Please select an option. Refer to --help for available options'.PHP_EOL;
    return;
}
