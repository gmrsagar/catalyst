# Catalyst [![Maintainability](https://api.codeclimate.com/v1/badges/fc59ecf978df906c9065/maintainability)](https://codeclimate.com/github/gmrsagar/catalyst/maintainability)

## Dependencies

### [PHPDotEnv](https://github.com/vlucas/phpdotenv)
Used for implementation of environment files to safely store credentials.

## Directives

__-u MySQL_username__ &nbsp; &nbsp; &nbsp; Provide the MySql Username.

__-p MySQL_password__ &nbsp; &nbsp; &nbsp; The password used to connect to MySQL.

__-h MySQL_host__ &nbsp; &nbsp; &nbsp; &nbsp; The host for MySQL instance.

__--file csv_file_name__ &nbsp; &nbsp; &nbsp; Parses the given csv file (filename without extension) and inserts each row to the users table.

__--create_table__ &nbsp; &nbsp; &nbsp; Create the users table.

__--help__ &nbsp; &nbsp; &nbsp; Display the list of available options.

__--dry_run__ &nbsp; &nbsp; &nbsp; Used with the --file directive. Parses the given file without database insertions.

## Installation and Activation

Composer update must be executed to install the dependencies.

Database 'catalyst' must exist or update the database name within the Database.php file.

The directives __-u, -p and -h__ must first be fired to set the MySQL credentials.

The users table must be created with the __--create_table__ directive.

Error log file can be found within the project directory if any error occurs during database insertions.

Example:

<pre>php user_uploads.php -u username -p password -h hostname --OPTION</pre>

## Thoughts & Assumptions

I had fun working on the app. 

The MySQL credentials include a fallback mode if not specified. The credentials can be set using environment variables.

Usage of env var is encouraged as writing passwords directly on cli is security risk as all the commands are recorded in history as well.

### Using environment variables

* Copy the .env.example as .env
* Set the credentials within the .env
