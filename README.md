# spaceship-backend

This project utilize (JSON Web Token) JWT auth to accomplish stateless and fast API authentication. 

## Installation

### Application

Clone this repository:
```
$ git clone https://github.com/helmithejoe/spaceship-backend.git
```
Go to project folder:
```
$ cd spaceship-backend
```
Run composer install:
```
$ composer install
```

### Database

Create a database and put the config inside application/config/database.php
```
$db['default'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => 'root',
	'password' => '',
	'database' => 'spaceship',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
```
Run database migration
```
$ php index.php migrate
```
Edit base_url inside application/config/config.php:
```
$config['base_url'] = 'http://localhost/';
```
Run the server:
```
$ sudo php -S 0.0.0.0:7000
```