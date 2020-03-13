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
CREATE TABLE users (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(24) NOT NULL,
	password CHAR(64) NOT NULL,
	email VARCHAR(99) NOT NULL
);
QUERY;
if (!$connection->query($query))
	exit("Failed to create table 'users'.");

$query = <<<'QUERY'
CREATE TABLE admins (
	id INT UNSIGNED PRIMARY KEY,
	FOREIGN KEY (id) REFERENCES users(id)
);
QUERY;
if (!$connection->query($query))
	exit("Failed to create table 'admins'.");

$query = <<<'QUERY'
CREATE TABLE posts (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	user_id INT UNSIGNED NOT NULL,
	content VARCHAR(512),
	FOREIGN KEY (user_id) REFERENCES users(id)
);
QUERY;
if (!$connection->query($query))
	exit("Failed to create table 'posts'.");

$query = <<<'QUERY'
CREATE TABLE comments (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	post_id INT UNSIGNED NOT NULL,
	user_id INT UNSIGNED NOT NULL,
	content VARCHAR(512) NOT NULL,
	FOREIGN KEY (post_id) REFERENCES posts(id),
	FOREIGN KEY (user_id) REFERENCES users(id)
);
QUERY;
if (!$connection->query($query))
	exit("Failed to create table 'comments'.");

$query = <<<'QUERY'
CREATE TABLE likes (
	post_id INT UNSIGNED NOT NULL,
	user_id INT UNSIGNED NOT NULL,
	PRIMARY KEY (post_id, user_id),
	FOREIGN KEY (post_id) REFERENCES posts(id),
	FOREIGN KEY (user_id) REFERENCES users(id)
);
QUERY;
if (!$connection->query($query))
	exit("Failed to create table 'comments'.");

echo "All tables created successfully." . PHP_EOL;
?>