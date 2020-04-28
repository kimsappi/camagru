#!/usr/bin/php
<?php
require_once("database.php");
try
{
	$connection = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
	exit("Failed to connect to database: $e" . PHP_EOL);
}

$query = <<<'QUERY'
CREATE TABLE IF NOT EXISTS users (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(24) UNIQUE NOT NULL,
	password CHAR(64) NOT NULL,
	email VARCHAR(99) UNIQUE NOT NULL,
	email_on_comment BOOLEAN DEFAULT 1,
	email_verified BOOLEAN DEFAULT 0
);
QUERY;
if (!$connection->query($query))
	exit("Failed to CREATE TABLE IF NOT EXISTS 'users'.");

// $query = <<<'QUERY'
// CREATE TABLE IF NOT EXISTS admins (
// 	id INT UNSIGNED PRIMARY KEY,
// 	FOREIGN KEY (id) REFERENCES users(id)
// );
// QUERY;
// if (!$connection->query($query))
// 	exit("Failed to CREATE TABLE IF NOT EXISTS 'admins'.");

$query = <<<'QUERY'
CREATE TABLE IF NOT EXISTS posts (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	user_id INT UNSIGNED NOT NULL,
	content VARCHAR(512),
	extension VARCHAR(4) NOT NULL,
	FOREIGN KEY (user_id) REFERENCES users(id)
);
QUERY;
if (!$connection->query($query))
	exit("Failed to CREATE TABLE IF NOT EXISTS 'posts'.");

$query = <<<'QUERY'
CREATE TABLE IF NOT EXISTS comments (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	post_id INT UNSIGNED NOT NULL,
	user_id INT UNSIGNED NOT NULL,
	content VARCHAR(512) NOT NULL,
	FOREIGN KEY (post_id) REFERENCES posts(id),
	FOREIGN KEY (user_id) REFERENCES users(id)
);
QUERY;
if (!$connection->query($query))
	exit("Failed to CREATE TABLE IF NOT EXISTS 'comments'.");

$query = <<<'QUERY'
CREATE TABLE IF NOT EXISTS likes (
	post_id INT UNSIGNED NOT NULL,
	user_id INT UNSIGNED NOT NULL,
	PRIMARY KEY (post_id, user_id),
	FOREIGN KEY (post_id) REFERENCES posts(id),
	FOREIGN KEY (user_id) REFERENCES users(id)
);
QUERY;
if (!$connection->query($query))
	exit("Failed to CREATE TABLE IF NOT EXISTS 'comments'.");

echo "All tables created successfully." . PHP_EOL;

/* Create admin account */
require_once("config.php");
require_once(__DIR__ . "/../functions/hashPassword.php");
$admin_password = hashPassword($admin_password, $admin_username, $salt);
$query = <<<QUERY
INSERT INTO users (`username`, `password`, `email`, `email_verified`)
	VALUES ('$admin_username', '$admin_password', '$admin_email', 1);
QUERY;
try
{
if (!$connection->query($query))
	exit("Failed to create admin account.");

echo "Admin account created successfully." . PHP_EOL;
}
catch(Exception $e)
{
	if (preg_match("/Integrity constraint violation.*for key 'username'/", $e))
		echo "Admin account already exists." . PHP_EOL;
	else
		echo $e . PHP_EOL;
}
?>
