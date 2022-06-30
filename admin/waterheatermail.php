<?php
error_reporting(E_ALL ^ E_NOTICE);

//***************************************
//USER VARIABLES Part 1 of 2
//***************************************
$config_file_location="/volume1/web/config/config_files/config_files_local/house_config.txt";

//***************************************
//START OF CODE
//***************************************

$data = file_get_contents("".$config_file_location."");
$pieces = explode(",", $data);
$email_address=$pieces[6];
$smtp_server=$pieces[22];	
$SMTPAuth_type=$pieces[23];
$smtp_user=$pieces[24];
$smtp_pass=$pieces[25];
$SMTPSecure_type=$pieces[26];
$smtp_port=$pieces[27];
$from_email_address=$pieces[28];

//***************************************
//USER VARIABLES Part 2 of 2
//***************************************
$database_column="waterheatermail";
$servername = "127.0.0.1:3307";
$username = "root";
$password = "password";
$dbname = "home_temp";
$message_subject='Water Heater Leak Detected';
$message_body='The water heater leak detector has been activated</b><br>Immediately check the house';
$message_altbody='The water heater leak detector has been activated. Immediately check the house';

//email address may have multiple addresses separated by a semicolon, let's split them apart
$to_email_exploded = explode(";", $email_address);


require $_SERVER['DOCUMENT_ROOT'].'/admin/vendor/phpmailer/phpmailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

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

$data = $pdo->query("SELECT * FROM `".$database_column."` WHERE row_num = 1");

$row = $data->fetch();
if ($row["datetime"]!="") {
	echo "database row returned with something. it returned with ".$row["datetime"]."<br><br>";

	$dteStart = new DateTime($row["datetime"]); 
	$dteEnd   = new DateTime($current_date);
	$interval = $dteStart->diff($dteEnd); //calculate the elapsed time between when the timer started and the current time
	
	if ($interval->format("%h") >0){
		
		//1 hour elapsed, clear out table to start 1 hour timer again
		print "More than 1 hour has elapsed since the last email was sent. Sending new notification email<br><br>";

		print "Sending new notification email<br><br>"; 
		
		//SMTP needs accurate times, and the PHP time zone MUST be set
		//This should be done in your php.ini, but this is how to do it if you don't have access to that
		date_default_timezone_set('Etc/UTC');


		require $_SERVER['DOCUMENT_ROOT']."/admin/vendor/autoload.php";

		//Create an instance; passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
			//Server settings
			$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
			$mail->isSMTP();                                            //Send using SMTP
			$mail->Host       = ''.$smtp_server.'';                     //Set the SMTP server to send through
			if($SMTPAuth_type==1){
				$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
			}else{
				$mail->SMTPAuth   = false;                                   //Disable SMTP authentication
			}
			$mail->Username   = ''.$smtp_user.'';                     //SMTP username
			$mail->Password   = ''.$smtp_pass.'';                               //SMTP password
			if($SMTPSecure_type=="ENCRYPTION_STARTTLS"){
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
			}else if($SMTPSecure_type=="ENCRYPTION_SMTPS"){
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable SSL
			}
			$mail->Port       = $smtp_port;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

			//Recipients
			$mail->setFrom(''.$from_email_address.'');
		
			foreach ($to_email_exploded as $to_email_addresses) {
				//echo "".$to_email_addresses."<br>";
				$mail->addAddress(''.$to_email_addresses.'');     //Add a recipient
			}
		
			$mail->addReplyTo(''.$from_email_address.'');

			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = $message_subject;
			$mail->Body    = $message_body;
			$mail->AltBody = $message_altbody;

			$mail->send();
			echo 'Message has been sent';
			$email_sent=1;
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			$email_sent=0;
		}

		if($email_sent==1){
			$sql = "UPDATE ".$database_column." SET `datetime` = ? WHERE row_num = 1";
			$pdo->prepare($sql)->execute([$current_date]);
		}
	}else{
		print "Notification E-Mail was sent less than 1 hour ago, test email will only send once per hour<br><br>";
	}
} else {
	echo "database row returned with nothing. it returned with ".$row["datetime"]."<br><br>";
	//SMTP needs accurate times, and the PHP time zone MUST be set
	//This should be done in your php.ini, but this is how to do it if you don't have access to that
	date_default_timezone_set('Etc/UTC');


	require $_SERVER['DOCUMENT_ROOT']."/admin/vendor/autoload.php";

	//Create an instance; passing `true` enables exceptions
	$mail = new PHPMailer(true);

	try {
		//Server settings
		$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
		$mail->isSMTP();                                            //Send using SMTP
		$mail->Host       = ''.$smtp_server.'';                     //Set the SMTP server to send through
		if($SMTPAuth_type==1){
			$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		}else{
			$mail->SMTPAuth   = false;                                   //Disable SMTP authentication
		}
		$mail->Username   = ''.$smtp_user.'';                     //SMTP username
		$mail->Password   = ''.$smtp_pass.'';                               //SMTP password
		if($SMTPSecure_type=="ENCRYPTION_STARTTLS"){
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
		}else if($SMTPSecure_type=="ENCRYPTION_SMTPS"){
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable SSL
		}
		$mail->Port       = $smtp_port;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

		//Recipients
		$mail->setFrom(''.$from_email_address.'');
		
		foreach ($to_email_exploded as $to_email_addresses) {
			//echo "".$to_email_addresses."<br>";
			$mail->addAddress(''.$to_email_addresses.'');     //Add a recipient
		}
		
		$mail->addReplyTo(''.$from_email_address.'');

		//Content
		$mail->isHTML(true);                                  //Set email format to HTML
		$mail->Subject = $message_subject;
		$mail->Body    = $message_body;
		$mail->AltBody = $message_altbody;
			
		$mail->send();
		echo 'Message has been sent';
		$email_sent=1;
	} catch (Exception $e) {
		echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		$email_sent=0;
	}

	//because no timer data is saved, save new timer data
	if($email_sent==1){
		$stmt = $pdo->prepare("INSERT INTO `".$database_column."` VALUES (?, ?, ?)");
		$stmt->execute([$current_date, 1, 1]);
	}
}
$pdo=null; //close the connection
?>