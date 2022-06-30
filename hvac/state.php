<?php
use PHPMailer\PHPMailer\PHPMailer;

require $_SERVER['DOCUMENT_ROOT'].'/admin/vendor/phpmailer/phpmailer/src/Exception.php';
require $_SERVER['DOCUMENT_ROOT'].'/admin/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/admin/vendor/phpmailer/phpmailer/src/SMTP.php';
if(!empty($_GET["state"]) AND is_numeric($_GET["state"])){
	$state = htmlspecialchars(strip_tags(trim(stripslashes($_GET["state"]))));
}  else {
    $state = 0;
}
if(!empty($_GET["column"])){
	$column = htmlspecialchars(strip_tags(trim(stripslashes($_GET["column"]))));
}  else {
    $column = 0;
}
print $state;
print "<br>";
print $column;
print "<br><br>";
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
	$error=1;
	if($column=="fan"){
		$sql = 'UPDATE status SET `fan` = ? WHERE row = 2';
		$error=0;
	}else if($column=="low_cool"){
		$sql = 'UPDATE status SET `low_cool` = ? WHERE row = 2';
		$error=0;
	}else if($column=="high_cool"){
		$sql = 'UPDATE status SET `high_cool` = ? WHERE row = 2';
		$error=0;
	}else if($column=="low_heat"){
		$sql = 'UPDATE status SET `low_heat` = ? WHERE row = 2';
		$error=0;
	}else if($column=="high_heat"){
		$sql = 'UPDATE status SET `high_heat` = ? WHERE row = 2';
		$error=0;
	}else if($column=="humidifier"){
		$sql = 'UPDATE status SET `humidifier` = ? WHERE row = 2';
		$error=0;
	}else if($column=="dehumidifier"){
		$sql = 'UPDATE status SET `dehumidifier` = ? WHERE row = 2';
		$error=0;
	}else if($column=="filter"){
		$sql = 'UPDATE status SET `filter` = ? WHERE row = 2';
		$error=0;
	}else if($column=="estop"){
		$sql = 'UPDATE status SET `estop` = ? WHERE row = 2';
		$error=0;
	}
		
	if($error==0){
		$pdo->prepare($sql)->execute([$state]);
	}else{
		echo "bad data received";
	}
	
if ($column =="filter" && $state==1){

	$current_date = date("Y-m-d H:i:s");//what is the time right now?
	$data = $pdo->query("SELECT * FROM `filtermail` WHERE row_num = 1");

	$row = $data->fetch();
	if ($row["datetime"]!="") {
		
		echo "database row returned with something. it returned with ".$row["datetime"]."<br><br>";
		$dteStart = new DateTime($row["datetime"]); 
		$dteEnd   = new DateTime($current_date);
		$interval = $dteStart->diff($dteEnd); //calculate the elapsed time between when the timer started and the current time
		
		if ($interval->format("%h") >0){
			
			//1 hour elapsed, clear out table to start 1 hour timer again
						
			$mail=new PHPMailer();
			$mail->CharSet = 'UTF-8';

			$body = 'This is the message';

			$mail->IsSMTP();
			$mail->Host       = 'smtp.server.com';

			$mail->SMTPSecure = 'yes';
			$mail->Port       = 587;
			$mail->SMTPDebug  = 1;
			$mail->SMTPAuth   = true;

			$mail->Username   = 'SMTP_user';
			$mail->Password   = 'SMTP_pass';

			$mail->SetFrom('from@doamin.com', $name);
			$mail->AddReplyTo('from@domain.com','no-reply');
			$mail->Subject    = 'Furnace Air Filter Dirty - Replace Filter';
			$mail->MsgHTML('Furnace Air Filter Dirty - Replace Filter');

			$mail->AddAddress('to_address1@domain.com');
			$mail->AddAddress('to_address2@domain.com');

			if (!$mail->send()) {
				echo 'Mailer Error: ' . $mail->ErrorInfo;
				$email_sent=0;
			} else {
				echo 'Message sent!';
				$email_sent=1;
			}
			
			if($email_sent==1){
				$sql = "UPDATE `filtermail` SET `datetime` = ? WHERE row_num = 1";
				$pdo->prepare($sql)->execute([$current_date]);
			}
			
		}
	} else {
		echo "database row returned with nothing. it returned with ".$row["datetime"]."<br><br>";
		$mail=new PHPMailer();
		$mail->CharSet = 'UTF-8';

		$body = 'This is the message';

		$mail->IsSMTP();
		$mail->Host       = 'smtp.server.com';

		$mail->SMTPSecure = 'yes';
		$mail->Port       = 587;
		$mail->SMTPDebug  = 1;
		$mail->SMTPAuth   = true;

		$mail->Username   = 'SMTP_user';
		$mail->Password   = 'SMTP_pass';

		$mail->SetFrom('from@doamin.com', $name);
		$mail->AddReplyTo('from@domain.com','no-reply');
		$mail->Subject    = 'Furnace Air Filter Dirty - Replace Filter';
		$mail->MsgHTML('Furnace Air Filter Dirty - Replace Filter');

		$mail->AddAddress('to_address1@domain.com');
		$mail->AddAddress('to_address2@domain.com');

		if (!$mail->send()) {
			echo 'Mailer Error: ' . $mail->ErrorInfo;
			$email_sent=0;
		} else {
			echo 'Message sent!';
			$email_sent=1;
		}
			
		if($email_sent==1){
			$stmt = $pdo->prepare("INSERT INTO `filtermail` VALUES (?, ?, ?)");
			$stmt->execute([$current_date, 1, 1]);
		}
	}
	$pdo=null; //close the connection

}else if ($column =="estop" && $state==1){
	
	$current_date = date("Y-m-d H:i:s");//what is the time right now?
	$data = $pdo->query("SELECT * FROM `estopmail` WHERE row_num = 1");

	$row = $data->fetch();
	if ($row["datetime"]!="") {
		echo "database row returned with something. it returned with ".$row["datetime"]."<br><br>";

		$dteStart = new DateTime($row["datetime"]); 
		$dteEnd   = new DateTime($current_date);
		$interval = $dteStart->diff($dteEnd); //calculate the elapsed time between when the timer started and the current time
		
		if ($interval->format("%h") >0){
			
			//1 hour elapsed, clear out table to start 1 hour timer again
			
			
			
			$mail=new PHPMailer();
			$mail->CharSet = 'UTF-8';
			
			
			$body = 'This is the message';

			$mail->IsSMTP();
			$mail->Host       = 'smtp.server.com';

			$mail->SMTPSecure = 'yes';
			$mail->Port       = 587;
			$mail->SMTPDebug  = 1;
			$mail->SMTPAuth   = true;

			$mail->Username   = 'SMTP_user';
			$mail->Password   = 'SMTP_pass';

			$mail->SetFrom('from@doamin.com', $name);
			$mail->AddReplyTo('from@domain.com','no-reply');
			$mail->Subject    = 'Furnace Condensate Switch E-Stop Activated!!!';
			$mail->MsgHTML('Furnace Condensate Switch E-Stop Activated!!!');

			$mail->AddAddress('to_address1@domain.com');
			$mail->AddAddress('to_address2@domain.com');

			if (!$mail->send()) {
				echo 'Mailer Error: ' . $mail->ErrorInfo;
				$email_sent=0;
			} else {
				echo 'Message sent!';
				$email_sent=1;
			}
			
			if($email_sent==1){
				$sql = "UPDATE `estopmail` SET `datetime` = ? WHERE row_num = 1";
				$pdo->prepare($sql)->execute([$current_date]);
			}	
		}
	} else {
		echo "database row returned with nothing. it returned with ".$row["datetime"]."<br><br>";
		$mail=new PHPMailer();
		$mail->CharSet = 'UTF-8';

		$body = 'This is the message';

		$mail->IsSMTP();
		$mail->Host       = 'smtp.server.com';

		$mail->SMTPSecure = 'yes';
		$mail->Port       = 587;
		$mail->SMTPDebug  = 1;
		$mail->SMTPAuth   = true;

		$mail->Username   = 'SMTP_user';
		$mail->Password   = 'SMTP_pass';

		$mail->SetFrom('from@doamin.com', $name);
		$mail->AddReplyTo('from@domain.com','no-reply');
		$mail->Subject    = 'Furnace Condensate Switch E-Stop Activated!!!';
		$mail->MsgHTML('Furnace Condensate Switch E-Stop Activated!!!');

		$mail->AddAddress('to_address1@domain.com');
		$mail->AddAddress('to_address2@domain.com');

		if (!$mail->send()) {
			echo 'Mailer Error: ' . $mail->ErrorInfo;
			$email_sent=0;
		} else {
			echo 'Message sent!';
			$email_sent=1;
		}
		if($email_sent==1){
			$stmt = $pdo->prepare("INSERT INTO `estopmail` VALUES (?, ?, ?)");
			$stmt->execute([$current_date, 1, 1]);
		}
	}
	$pdo=null; //close the connection
}
?>