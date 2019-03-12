# Catalyst [![Maintainability](https://api.codeclimate.com/v1/badges/fc59ecf978df906c9065/maintainability)](https://codeclimate.com/github/gmrsagar/catalyst/maintainability)

This is a PHP CLI app that  accepts a CSV file as input and parses the file data to be
inserted into a MySQL database. The CSV file with test data is included in this repo.

## Dependencies

### [PHPDotEnv](https://github.com/vlucas/phpdotenv)
This dependency is used in the app to create environment files inorder to store the credentials safely.

## Directives

The app makes use of the following directives

| Directive | Description  | 
|----|---|
| -u MySQL_username    | For providing the MySql Username |
| -p MySQL_password    | For providing the password used to connect to MySQL |
| -h MySQL_host        | For providing the host for MySQL instance |
| --file csv_file_name | To parse the given csv file (filename without extension) and insert each row to the users table |
| --create_table       | To create the users table |
| --help               | To display the list of available options |
| --dry_run            | Used with the --file directive. Parses the given file without database insertions  |


## Installation and Activation

Run `composer install` to install the dependencies.

Run `php user_uploads.php -u username -p password -h hostname --OPTION` to start the app.

The default database used is named 'catalyst'. To specify your own database, update the the database name in Database.php file accordingly.

MySQL credentials can be provided using the directives __-u MySQL_username -p MySQL_password and -h MySQL_hostname__ .

The users table can be created with the __--create_table__ directive.

Error log file can be found in error.php within the project directory(if any error occurs during database insertions).


## Thoughts & Assumptions

The MySQL credentials include a fallback mode to get credentials from .env file if not specified explicitly. The credentials can be set using environment variables.

Usage of environment variables is encouraged as writing passwords directly on cli is security risk as all the commands are recorded in history as well.

The username, password and hostname must either be set on the environment variables or provided on each run(except on --dry_run).

Each directive is individually run except for --dry_run which is used with the --file directive.

The CSV filename is provided without extension (extension will be added automatically).

### Using environment variables

* Copy the .env.example as .env
* Set the credentials within the .env
