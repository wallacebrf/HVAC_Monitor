<?php
if(!empty($_GET["duration"]) AND is_numeric($_GET["duration"])){
    $duration = htmlspecialchars(strip_tags(trim(stripslashes($_GET["duration"]))));
}  else {
    $duration = 0;
}
$servername = "127.0.0.1:3307";
$username = "root";
$password = "password";
$dbname = "hvac";

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
	$sql = "INSERT INTO `fan_log` (`duration`) 
		VALUES (?)";
	$pdo->prepare($sql)->execute([$duration]);
	
$pdo=null; //close the connection

?>