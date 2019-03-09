<?php

require_once __DIR__ . '/vendor/autoload.php';

use Database\Database;

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
            //file parsing code
        break;

        default:
            echo "i dont know  this";
        }
    }
    else {
        echo "invalid parameter";
    }
