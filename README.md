# Catalyst [![Maintainability](https://api.codeclimate.com/v1/badges/fc59ecf978df906c9065/maintainability)](https://codeclimate.com/github/gmrsagar/catalyst/maintainability)

## Dependencies

### [PHPDotEnv](https://github.com/vlucas/phpdotenv)
Used for implement environment files to safely store credentials.

## Directives

-u MySQL_username        Provide the MySql Username.
-p MySQL_password        The password used to connect to MySQL.
-h MySQL_host            The host for MySQL instance.
--file csv_file_name     Parses the given csv file and inserts each row to the users table.
--create_table           Create the users table.
--help                   Display the list of available options.
--dry_run                Used with the --file directive. Parses the given file without database insertions.

## Installation and Activation

Composer update must be executed to install the dependencies.
The directives __-u, -p and -h__ must first be fired to set the MySQL credentials. The database used is called 'catalyst'. 
The users table must be created with the __--create_table__ directive.
Error log file can be found within the project directory if any error occurs during database insertions.

