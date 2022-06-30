<?php
error_reporting(E_ALL ^ E_NOTICE);
$servername = "127.0.0.1:3307";
$username = "root";
$password = "password";
$dbname = "home_temp";

// Create connection
$charset = 'utf8mb4';
$dsn = "mysql:host=$servername;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
     $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
	$current_date = date("Y-m-d H:i:s");//what is the time right now?
	$sql = "UPDATE `waterheatermonitor` SET `datetime` = ? WHERE id = 1";
	$pdo->prepare($sql)->execute([$current_date]);
	
$pdo=null; //close the connection
?>