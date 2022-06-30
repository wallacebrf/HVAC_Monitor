<?php
if($_SERVER['HTTPS']!="on") {

$redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

header("Location:$redirect"); } 


// Initialize the session
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
error_reporting(E_ALL ^ E_NOTICE);
include $_SERVER['DOCUMENT_ROOT']."/functions.php";
$current_date = new DateTime(date('Y/m/d H:i:s'));
$servername = "127.0.0.1:3307";
$username = "root";
$password = "password";
$dbname = "hvac";
$dbname2 = "home_temp";
$generic_error="";

//***************************************
//INPUT VARIABLE VALIDATION
//***************************************
if(!empty($_GET["log_type"])){
	[$log_type, $generic_error] = test_input_processing($_GET['log_type'], 4, "numeric", 1, 4);
}  else {
    $log_type = 4;
}

if(!empty($_GET["log_year"])){
	[$log_year, $generic_error] = test_input_processing($_GET['log_year'], $current_date->format('Y'), "numeric", 1970, 2100);
}  else {
    $log_year = 0;
}

if(!empty($_GET["log_month"])){
	[$log_month, $generic_error] = test_input_processing($_GET['log_month'], $current_date->format('m'), "numeric", 1, 12);
}  else {
    $log_month = 0;
}

if(!empty($_GET["log_day"])){
	[$log_day, $generic_error] = test_input_processing($_GET['log_day'], $current_date->format('d'), "numeric", 1, 31);
}  else {
    $log_day = 0;
}

if(!empty($_GET["show_logs"])){
	[$show_logs, $generic_error] = test_input_processing($_GET['show_logs'], 0, "numeric", 0, 1);
}  else {
    $show_logs = 0;
}


//***************************************
//CHECK ON WATER HEATER STATUS
//***************************************
// Create connection
$charset = 'utf8mb4';
$dsn = "mysql:host=$servername;dbname=$dbname2;charset=$charset";
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

$data = $pdo->query("SELECT * FROM `waterheatermonitor`");
if ($data) {
	while ($row = $data->fetch())
	{
		$last_update = new DateTime($row["datetime"]);	 
		$interval = $current_date->diff($last_update);
	 
		if ($interval->format('%y')==0 && $interval->format('%m')==0 && $interval->format('%d')==0 && $interval->format('%h')==0 && $interval->format('%i') <2){
			$water_heater_ok=true;
		}else{
			$water_heater_ok=false;
		}
	}
}else{
	print "Error, Database Returned ZERO rows of data. Database may be corrupted";
}
//close the connection
$pdo=null;

//***************************************
//GATHER ALL OF THE FURNACE, AC, DEHUMIDIDER, AND HUMIDIFIER INFORMATION
//***************************************

$dsn = "mysql:host=$servername;dbname=$dbname;charset=$charset";
try {
     $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
$data = $pdo->query("SELECT * FROM `status`");

if ($data) {
    // output data of each row
    print "<center><table border=\"1\" cellspacing=\"1\" cellpadding=\"1\">
			<tr>
				<td align=\"center\"><b>Fan</b></td>
				<td align=\"center\"><b>Cool Stage 1</b></td>
				<td align=\"center\"><b>Cool Stage 2</b></td>
				<td align=\"center\"><b>Heat Stage 1</b></td>
				<td align=\"center\"><b>Heat Stage 2</b></td>
				<td align=\"center\"><b>Humidifier</b></td>
				<td align=\"center\"><b>De-Humidifier</b></td>
				<td align=\"center\"><b>HVAC Filter</b></td>
				<td align=\"center\"><b>HVAC E-Stop</b></td>
				<td align=\"center\"><b>Water Heater</b></td>
			</tr>";
	while ($row = $data->fetch()){
      	echo "<tr><td align=\"center\">";
		if ($row["fan"]==0){
			print "<img src=\"red.png\" alt=";
			print $row["fan"];
			print ">";
		}else if ($row["fan"]==1){
			print "<img src=\"green.png\" alt=";
			print $row["fan"];
			print ">";
		}
      	echo "</td><td align=\"center\">";
      	if ($row["low_cool"]==0){
			print "<img src=\"red.png\" alt=";
			print $row["low_cool"];
			print ">";
		}else if ($row["low_cool"]==1){
			print "<img src=\"green.png\" alt=";
			print $row["low_cool"];
			print ">";
		}
      	echo "</td><td align=\"center\">";
		if ($row["high_cool"]==0){
			print "<img src=\"red.png\" alt=";
			print $row["high_cool"];
			print ">";
		}else if ($row["high_cool"]==1){
			print "<img src=\"green.png\" alt=";
			print $row["high_cool"];
			print ">";
		}
      	echo "</td><td align=\"center\">";
		if ($row["low_heat"]==0){
			print "<img src=\"red.png\" alt=";
			print $row["low_heat"];
			print ">";
		}else if ($row["low_heat"]==1){
			print "<img src=\"green.png\" alt=";
			print $row["low_heat"];
			print ">";
		}
      	echo "</td><td align=\"center\">";
		if ($row["high_heat"]==0){
			print "<img src=\"red.png\" alt=";
			print $row["high_heat"];
			print ">";
		}else if ($row["high_heat"]==1){
			print "<img src=\"green.png\" alt=";
			print $row["high_heat"];
			print ">";
		}
      	echo "</td><td align=\"center\">";
		if ($row["humidifier"]==0){
			print "<img src=\"red.png\" alt=";
			print $row["humidifier"];
			print ">";
		}else if ($row["humidifier"]==1){
			print "<img src=\"green.png\" alt=";
			print $row["humidifier"];
			print ">";
		}
      	echo "</td><td align=\"center\">";
		if ($row["dehumidifier"]==0){
			print "<img src=\"red.png\" alt=";
			print $row["dehumidifier"];
			print ">";
		}else if ($row["dehumidifier"]==1){
			print "<img src=\"green.png\" alt=";
			print $row["dehumidifier"];
			print ">";
		}
		echo "</td><td align=\"center\">";
		if ($row["filter"]==0){
			print "<img src=\"green.png\" alt=";
			print $row["filter"];
			print ">";
		}else if ($row["filter"]==1){
			print "<img src=\"red.png\" alt=";
			print $row["filter"];
			print ">";
		}
		echo "</td><td align=\"center\">";
		if ($row["estop"]==0){
			print "<img src=\"green.png\" alt=";
			print $row["estop"];
			print ">";
		}else if ($row["estop"]==1){
			print "<img src=\"red.png\" alt=";
			print $row["estop"];
			print ">";
		}
    }
		if ($water_heater_ok==true){
			print "</td><td align=\"center\"><img src=\"green.png\" alt=";
			print $last_update->format('Y/m/d h:i:s');
			print ">";
		}else{
			print "</td><td align=\"center\"><img src=\"red.png\" alt=";
			print $last_update->format('Y/m/d h:i:s');
			print "><br>";
			print $last_update->format('Y/m/d h:i:s');
		}
		echo "</td></tr>";
    print "</table></center>";
} else {
    echo "0 results";
}

print "	<center>
			<br><br><fieldset>
				<legend>Select HVAC Log Details:</legend>
				<form action=\"index.php\" method=\"get\">
					<input type=\"checkbox\" name=\"show_logs\" value=\"1\"";
					if ($show_logs ==1){
						print " checked";
					}
					print ">Show Logs? || 
					Log Type: <select name=\"log_type\">";
					if ($log_type==1){
						print "<option value=\"1\" selected>Yearly</option>
						<option value=\"2\">Monthly</option>
						<option value=\"3\">Weekly</option>
						<option value=\"4\">Daily</option>";
					}else if ($log_type==2){
						print "<option value=\"1\">Yearly</option>
						<option value=\"2\" selected>Monthly</option>
						<option value=\"3\">Weekly</option>
						<option value=\"4\">Daily</option>";
					}else if ($log_type == 3){
						print "<option value=\"1\">Yearly</option>
						<option value=\"2\">Monthly</option>
						<option value=\"3\" selected>Weekly</option>
						<option value=\"4\">Daily</option>";
					}else if ($log_type == 4){
						print "<option value=\"1\">Yearly</option>
						<option value=\"2\">Monthly</option>
						<option value=\"3\">Weekly</option>
						<option value=\"4\" selected>Daily</option>";
					}
						
					print "</select>
					Year: <select name=\"log_year\">";
					if ($log_year==0){
						print "<option value=\"".$current_date->format('Y')."\" selected>".$current_date->format('Y')."</option>";
					}else{
						print "<option value=\"".$current_date->format('Y')."\">".$current_date->format('Y')."</option>";
					}
					for ($x=0;$x<=4;$x++){
						$year = $current_date->sub(new DateInterval('P1Y'));
						if ($log_year==$year->format('Y')){
							print "<option value=\"".$year->format('Y')."\" selected>".$year->format('Y')."</option>";
						}else{
							print "<option value=\"".$year->format('Y')."\">".$year->format('Y')."</option>";
						}
					}						
					print "</select>
					Month:<select name=\"log_month\">";
					for ($x=1;$x<=12;$x++){
						if ($log_month==0){
							if ($x==$current_date->format('m')){
								if ($x<10){
									print "<option value=\"0".$x."\" selected>0".$x."</option>";
								}else{
									print "<option value=\"".$x."\" selected>".$x."</option>";
								}
							}else{
								if ($x<10){
									print "<option value=\"0".$x."\">0".$x."</option>";
								}else{
									print "<option value=\"".$x."\">".$x."</option>";
								}
							}
						}else{
							if ($x==$log_month){
								if ($x<10){
									print "<option value=\"0".$x."\" selected>0".$x."</option>";
								}else{
									print "<option value=\"".$x."\" selected>".$x."</option>";
								}
							}else{
								if ($x<10){
									print "<option value=\"0".$x."\">0".$x."</option>";
								}else{
									print "<option value=\"".$x."\">".$x."</option>";
								}
							}
						}
					}	
					print "</select>	
					Day:<select name=\"log_day\">";
					for ($x=1;$x<=31;$x++){
						if ($log_day==0){
							if ($x==$current_date->format('d')){
								if ($x<10){
									print "<option value=\"0".$x."\" selected>0".$x."</option>";
								}else{
									print "<option value=\"".$x."\" selected>".$x."</option>";
								}
							}else{
								if ($x<10){
									print "<option value=\"0".$x."\">0".$x."</option>";
								}else{
									print "<option value=\"".$x."\">".$x."</option>";
								}
							}
						}else{
							if ($x==$log_day){
								if ($x<10){
									print "<option value=\"0".$x."\" selected>0".$x."</option>";
								}else{
									print "<option value=\"".$x."\" selected>".$x."</option>";
								}
							}else{
								if ($x<10){
									print "<option value=\"0".$x."\">0".$x."</option>";
								}else{
									print "<option value=\"".$x."\">".$x."</option>";
								}
							}
						}
					}	
					print "</select>";					
					print "<br><input type=\"hidden\" id=\"page\" name=\"page\" value=\"7\"><input type=\"submit\" value=\"Submit\">
				</form>";
if ($show_logs ==1){
	if ($log_type==4){//daily
		$submitted_date = "".$log_year."-".$log_month."-".$log_day."";
		$submitted_date_object = new DateTime($submitted_date);
		$previous_day = $submitted_date_object->sub(new DateInterval('P1D'));
		$submitted_date_object = new DateTime($submitted_date);
		$next_day = $submitted_date_object->add(new DateInterval('P1D'));
		print "<a href=\"".$home."/index.php?log_year=".$previous_day->format('Y')."&log_month=".$previous_day->format('m')."&log_day=".$previous_day->format('d')."&page=7&show_logs=1&log_type=".$log_type."\">  |  Previous Day  |</a>
				<a href=\"".$home."/index.php?log_year=".$next_day->format('Y')."&log_month=".$next_day->format('m')."&log_day=".$next_day->format('d')."&page=7&show_logs=1&log_type=".$log_type."\">  |  Next Day  |</a>";
	}else if($log_type==3){//weekly
		$submitted_date = "".$log_year."-".$log_month."-".$log_day."";
		$submitted_date_object = new DateTime($submitted_date);
		$previous_day = $submitted_date_object->sub(new DateInterval('P7D'));
		$submitted_date_object = new DateTime($submitted_date);
		$next_day = $submitted_date_object->add(new DateInterval('P7D'));
		print "<a href=\"".$home."/index.php?log_year=".$previous_day->format('Y')."&log_month=".$previous_day->format('m')."&log_day=".$previous_day->format('d')."&page=7&show_logs=1&log_type=".$log_type."\">  |  Previous Week  |</a>
			<a href=\"".$home."/index.php?log_year=".$next_day->format('Y')."&log_month=".$next_day->format('m')."&log_day=".$next_day->format('d')."&page=7&show_logs=1&log_type=".$log_type."\">  |  Next Week  |</a>";
	}else if($log_type==2){//montly
		$submitted_date = "".$log_year."-".$log_month."-".$log_day."";
		$submitted_date_object = new DateTime($submitted_date);
		$previous_day = $submitted_date_object->sub(new DateInterval('P1M'));
		$submitted_date_object = new DateTime($submitted_date);
		$next_day = $submitted_date_object->add(new DateInterval('P1M'));
		print "<a href=\"".$home."/index.php?log_year=".$previous_day->format('Y')."&log_month=".$previous_day->format('m')."&log_day=".$previous_day->format('d')."&page=7&show_logs=1&log_type=".$log_type."\">  |  Previous Month  |</a>
				<a href=\"".$home."/index.php?log_year=".$next_day->format('Y')."&log_month=".$next_day->format('m')."&log_day=".$next_day->format('d')."&page=7&show_logs=1&log_type=".$log_type."\">  |  Next Month  |</a>";
	}else if($log_type==1){//yearly
		$submitted_date = "".$log_year."-".$log_month."-".$log_day."";
		$submitted_date_object = new DateTime($submitted_date);
		$previous_day = $submitted_date_object->sub(new DateInterval('P1Y'));
		$submitted_date_object = new DateTime($submitted_date);
		$next_day = $submitted_date_object->add(new DateInterval('P1Y'));
		print "<a href=\"".$home."/index.php?log_year=".$previous_day->format('Y')."&log_month=".$previous_day->format('m')."&log_day=".$previous_day->format('d')."&page=7&show_logs=1&log_type=".$log_type."\">  |  Previous Year  |</a>
				<a href=\"".$home."/index.php?log_year=".$next_day->format('Y')."&log_month=".$next_day->format('m')."&log_day=".$next_day->format('d')."&page=7&show_logs=1&log_type=".$log_type."\">  |  Next Year  |</a>";
	}
}
		print "</fieldset></center>";
$sql_log_type=0;
if ($log_type==4 && $show_logs==1){//Daily
	if ($log_year ==0 && $log_month==0 && $log_day==0){
		$search_date = date("Y-m-d"); //IF WE ARE NOT SEARCHING FOR A SPECIFIC DATE, DEFAULT TO THE CURRENT DATE
	}else{
		$search_date = new DateTime("".$log_year."-".$log_month."-".$log_day."");
	}
	$sql_log_type=1; //only the daily logs show the duration of the individual cycles, save this for the future
	
	Print "<br>Currently Displaying Data for: ";
	print $search_date->format('Y-m-d');
	print "<br><br>";
}else if ($log_type == 3 && $show_logs==1){//weekly
	$end_date = new DateTime("".$log_year."-".$log_month."-".$log_day."");
	$past_date = $end_date->sub(new DateInterval('P7D'));
	$end_date = new DateTime("".$log_year."-".$log_month."-".$log_day."");
	$end_date = $end_date->add(new DateInterval('P1D'));
	$sql_log_type=2;
		
	Print "<br>Currently Displaying Data Between ";
	print $past_date->format('Y/m/d');
	print " and ";
	print $end_date->format('Y/m/d');
	print "<br><br>";

}else if ($log_type == 2 && $show_logs==1){//monthly
	$end_date = new DateTime("".$log_year."-".$log_month."-".$log_day."");
	$past_date = $end_date->sub(new DateInterval('P1M'));
	$end_date = new DateTime("".$log_year."-".$log_month."-".$log_day."");
	$end_date = $end_date->add(new DateInterval('P1D'));
	$sql_log_type=2;
		
	Print "<br>Currently Displaying Between ";
	print $past_date->format('Y/m/d');
	print " and ";
	print $end_date->format('Y/m/d');
	print "<br><br>";
	
}else if ($log_type == 1 && $show_logs==1){//yearly
	$end_date = new DateTime("".$log_year."-".$log_month."-".$log_day."");
	$past_date = $end_date->sub(new DateInterval('P1Y'));
	$end_date = new DateTime("".$log_year."-".$log_month."-".$log_day."");
	$end_date = $end_date->add(new DateInterval('P1D'));
	$sql_log_type=2;
	
	Print "<br>Currently Displaying Data Between ";
	print $past_date->format('Y/m/d');
	print " and ";
	print $end_date->format('Y/m/d');
	print "<br><br>";
}

if ($show_logs==1){
	if($sql_log_type==1){ //we are only looking at one date, this is generally used by daily dates only
		//**************************
		//PREPARE SQL QURIES
		//**************************
		$sql_dehumidifier_log = $pdo->prepare('SELECT * FROM dehumidifier_log WHERE `timeStamp` LIKE ? ORDER BY `timeStamp` DESC');
		$sql_high_cool_log = $pdo->prepare("SELECT * FROM high_cool_log WHERE `timeStamp` LIKE ? ORDER BY `timeStamp` DESC");
		$sql_high_heat_log = $pdo->prepare("SELECT * FROM high_heat_log WHERE `timeStamp` LIKE ? ORDER BY `timeStamp` DESC");
		$sql_humidifier_log = $pdo->prepare("SELECT * FROM humidifier_log WHERE `timeStamp` LIKE ? ORDER BY `timeStamp` DESC");
		$sql_low_cool_log = $pdo->prepare("SELECT * FROM low_cool_log WHERE `timeStamp` LIKE ? ORDER BY `timeStamp` DESC");
		$sql_low_heat_log = $pdo->prepare("SELECT * FROM low_heat_log WHERE `timeStamp` LIKE ? ORDER BY `timeStamp` DESC");
		$sql_fan_log = $pdo->prepare("SELECT * FROM fan_log WHERE `timeStamp` LIKE ? ORDER BY `timeStamp` DESC");
		
		
		$sql_dehumidifier_average_duration = $pdo->prepare('SELECT AVG(duration) FROM dehumidifier_log WHERE `timeStamp` LIKE ?');
		$sql_high_cool_log_average_duration = $pdo->prepare('SELECT AVG(duration) FROM high_cool_log WHERE `timeStamp` LIKE ?');
		$sql_high_heat_log_average_duration = $pdo->prepare('SELECT AVG(duration) FROM high_heat_log WHERE `timeStamp` LIKE ?');
		$sql_humidifier_log_average_duration = $pdo->prepare('SELECT AVG(duration) FROM humidifier_log WHERE `timeStamp` LIKE ?');
		$sql_low_cool_log_average_duration = $pdo->prepare('SELECT AVG(duration) FROM low_cool_log WHERE `timeStamp` LIKE ?');
		$sql_low_heat_log_average_duration = $pdo->prepare('SELECT AVG(duration) FROM low_heat_log WHERE `timeStamp` LIKE ?');
		$sql_fan_log_average_duration = $pdo->prepare('SELECT AVG(duration) FROM fan_log WHERE `timeStamp` LIKE ?');
		
		
		$sql_dehumidifier_count = $pdo->prepare('SELECT COUNT(duration) FROM dehumidifier_log WHERE `timeStamp` LIKE ?');
		$sql_high_cool_log_count = $pdo->prepare('SELECT COUNT(duration) FROM high_cool_log WHERE `timeStamp` LIKE ?');
		$sql_high_heat_log_count = $pdo->prepare('SELECT COUNT(duration) FROM high_heat_log WHERE `timeStamp` LIKE ?');
		$sql_humidifier_log_count = $pdo->prepare('SELECT COUNT(duration) FROM humidifier_log WHERE `timeStamp` LIKE ?');
		$sql_low_cool_log_count = $pdo->prepare('SELECT COUNT(duration) FROM low_cool_log WHERE `timeStamp` LIKE ?');
		$sql_low_heat_log_count = $pdo->prepare('SELECT COUNT(duration) FROM low_heat_log WHERE `timeStamp` LIKE ?');
		$sql_fan_log_count = $pdo->prepare('SELECT COUNT(duration) FROM fan_log WHERE `timeStamp` LIKE ?');
		
		
		//**************************
		//EXECUTE AND FETCH SQL QURIES
		//**************************
		if ($log_type==4){//Daily
			$sql_dehumidifier_log->execute(["%".$search_date->format('Y-m-d')."%"]);
			$sql_high_cool_log->execute(["%".$search_date->format('Y-m-d')."%"]);
			$sql_high_heat_log->execute(["%".$search_date->format('Y-m-d')."%"]);
			$sql_humidifier_log->execute(["%".$search_date->format('Y-m-d')."%"]);
			$sql_low_cool_log->execute(["%".$search_date->format('Y-m-d')."%"]);
			$sql_low_heat_log->execute(["%".$search_date->format('Y-m-d')."%"]);
			$sql_fan_log->execute(["%".$search_date->format('Y-m-d')."%"]);
			
		}
		$sql_dehumidifier_average_duration->execute(["%".$search_date->format('Y-m-d')."%"]);
		$dehumidifier_log_average_duration = $sql_dehumidifier_average_duration->fetch();

		$sql_high_cool_log_average_duration->execute(["%".$search_date->format('Y-m-d')."%"]);
		$high_cool_log_average_duration = $sql_high_cool_log_average_duration->fetch();

		$sql_high_heat_log_average_duration->execute(["%".$search_date->format('Y-m-d')."%"]);
		$high_heat_log_average_duration = $sql_high_heat_log_average_duration->fetch();

		$sql_humidifier_log_average_duration->execute(["%".$search_date->format('Y-m-d')."%"]);
		$humidifier_log_average_duration = $sql_humidifier_log_average_duration->fetch();

		$sql_low_cool_log_average_duration->execute(["%".$search_date->format('Y-m-d')."%"]);
		$low_cool_log_average_duration = $sql_low_cool_log_average_duration->fetch();

		$sql_low_heat_log_average_duration->execute(["%".$search_date->format('Y-m-d')."%"]);
		$low_heat_log_average_duration = $sql_low_heat_log_average_duration->fetch();

		$sql_fan_log_average_duration->execute(["%".$search_date->format('Y-m-d')."%"]);
		$fan_log_average_duration = $sql_fan_log_average_duration->fetch();
		
		$sql_dehumidifier_count->execute(["%".$search_date->format('Y-m-d')."%"]);
		$dehumidifier_log_count = $sql_dehumidifier_count->fetch();
		
		$sql_high_cool_log_count->execute(["%".$search_date->format('Y-m-d')."%"]);
		$high_cool_log_count = $sql_high_cool_log_count->fetch();
		
		$sql_high_heat_log_count->execute(["%".$search_date->format('Y-m-d')."%"]);
		$high_heat_log_count = $sql_high_heat_log_count->fetch();
		
		$sql_humidifier_log_count->execute(["%".$search_date->format('Y-m-d')."%"]);
		$humidifier_log_count = $sql_humidifier_log_count->fetch();
		
		$sql_low_cool_log_count->execute(["%".$search_date->format('Y-m-d')."%"]);
		$low_cool_log_count = $sql_low_cool_log_count->fetch();
		
		$sql_low_heat_log_count->execute(["%".$search_date->format('Y-m-d')."%"]);
		$low_heat_log_count = $sql_low_heat_log_count->fetch();
		
		$sql_fan_log_count->execute(["%".$search_date->format('Y-m-d')."%"]);
		$fan_log_count = $sql_fan_log_count->fetch();
		
	}else if($sql_log_type==2){
		$sql_dehumidifier_average_duration = $pdo->prepare('SELECT AVG(duration) FROM dehumidifier_log WHERE `timeStamp` BETWEEN cast(?  as datetime) AND cast(? as datetime)');
		$sql_high_cool_log_average_duration = $pdo->prepare('SELECT AVG(duration) FROM high_cool_log WHERE `timeStamp` BETWEEN cast(?  as datetime) AND cast(? as datetime)');
		$sql_high_heat_log_average_duration = $pdo->prepare('SELECT AVG(duration) FROM high_heat_log WHERE `timeStamp` BETWEEN cast(?  as datetime) AND cast(? as datetime)');
		$sql_humidifier_log_average_duration = $pdo->prepare('SELECT AVG(duration) FROM humidifier_log WHERE `timeStamp` BETWEEN cast(?  as datetime) AND cast(? as datetime)');
		$sql_low_cool_log_average_duration = $pdo->prepare('SELECT AVG(duration) FROM low_cool_log WHERE `timeStamp` BETWEEN cast(?  as datetime) AND cast(? as datetime)');
		$sql_low_heat_log_average_duration = $pdo->prepare('SELECT AVG(duration) FROM low_heat_log WHERE `timeStamp` BETWEEN cast(?  as datetime) AND cast(? as datetime)');
		$sql_fan_log_average_duration = $pdo->prepare('SELECT AVG(duration) FROM fan_log WHERE `timeStamp` BETWEEN cast(?  as datetime) AND cast(? as datetime)');
		
		
		$sql_dehumidifier_count = $pdo->prepare('SELECT COUNT(duration) FROM dehumidifier_log WHERE `timeStamp` BETWEEN cast(?  as datetime) AND cast(? as datetime)');
		$sql_high_cool_log_count = $pdo->prepare('SELECT COUNT(duration) FROM high_cool_log WHERE `timeStamp` BETWEEN cast(?  as datetime) AND cast(? as datetime)');
		$sql_high_heat_log_count = $pdo->prepare('SELECT COUNT(duration) FROM high_heat_log WHERE `timeStamp` BETWEEN cast(?  as datetime) AND cast(? as datetime)');
		$sql_humidifier_log_count = $pdo->prepare('SELECT COUNT(duration) FROM humidifier_log WHERE `timeStamp` BETWEEN cast(?  as datetime) AND cast(? as datetime)');
		$sql_low_cool_log_count = $pdo->prepare('SELECT COUNT(duration) FROM low_cool_log WHERE `timeStamp` BETWEEN cast(?  as datetime) AND cast(? as datetime)');
		$sql_low_heat_log_count = $pdo->prepare('SELECT COUNT(duration) FROM low_heat_log WHERE `timeStamp` BETWEEN cast(?  as datetime) AND cast(? as datetime)');
		$sql_fan_log_count = $pdo->prepare('SELECT COUNT(duration) FROM fan_log WHERE `timeStamp` BETWEEN cast(?  as datetime) AND cast(? as datetime)');
		
		//**************************
		//EXECUTE AND FETCH SQL QURIES
		//**************************
		$sql_dehumidifier_average_duration->execute(["".$past_date->format('Y/m/d')."", "".$end_date->format('Y/m/d').""]);
		$dehumidifier_log_average_duration = $sql_dehumidifier_average_duration->fetch();

		$sql_high_cool_log_average_duration->execute(["".$past_date->format('Y/m/d')."", "".$end_date->format('Y/m/d').""]);
		$high_cool_log_average_duration = $sql_high_cool_log_average_duration->fetch();

		$sql_high_heat_log_average_duration->execute(["".$past_date->format('Y/m/d')."", "".$end_date->format('Y/m/d').""]);
		$high_heat_log_average_duration = $sql_high_heat_log_average_duration->fetch();

		$sql_humidifier_log_average_duration->execute(["".$past_date->format('Y/m/d')."", "".$end_date->format('Y/m/d').""]);
		$humidifier_log_average_duration = $sql_humidifier_log_average_duration->fetch();

		$sql_low_cool_log_average_duration->execute(["".$past_date->format('Y/m/d')."", "".$end_date->format('Y/m/d').""]);
		$low_cool_log_average_duration = $sql_low_cool_log_average_duration->fetch();

		$sql_low_heat_log_average_duration->execute(["".$past_date->format('Y/m/d')."", "".$end_date->format('Y/m/d').""]);
		$low_heat_log_average_duration = $sql_low_heat_log_average_duration->fetch();

		$sql_fan_log_average_duration->execute(["".$past_date->format('Y/m/d')."", "".$end_date->format('Y/m/d').""]);
		$fan_log_average_duration = $sql_fan_log_average_duration->fetch();
		
		$sql_dehumidifier_count->execute(["".$past_date->format('Y/m/d')."", "".$end_date->format('Y/m/d').""]);
		$dehumidifier_log_count = $sql_dehumidifier_count->fetch();
		
		$sql_high_cool_log_count->execute(["".$past_date->format('Y/m/d')."", "".$end_date->format('Y/m/d').""]);
		$high_cool_log_count = $sql_high_cool_log_count->fetch();
		
		$sql_high_heat_log_count->execute(["".$past_date->format('Y/m/d')."", "".$end_date->format('Y/m/d').""]);
		$high_heat_log_count = $sql_high_heat_log_count->fetch();
		
		$sql_humidifier_log_count->execute(["".$past_date->format('Y/m/d')."", "".$end_date->format('Y/m/d').""]);
		$humidifier_log_count = $sql_humidifier_log_count->fetch();
		
		$sql_low_cool_log_count->execute(["".$past_date->format('Y/m/d')."", "".$end_date->format('Y/m/d').""]);
		$low_cool_log_count = $sql_low_cool_log_count->fetch();
		
		$sql_low_heat_log_count->execute(["".$past_date->format('Y/m/d')."", "".$end_date->format('Y/m/d').""]);
		$low_heat_log_count = $sql_low_heat_log_count->fetch();
		
		$sql_fan_log_count->execute(["".$past_date->format('Y/m/d')."", "".$end_date->format('Y/m/d').""]);
		$fan_log_count = $sql_fan_log_count->fetch();
	
	}
	


print "<center>
			<table>
				<tr>
					<td colspan=\"4\"><center><h4>";
					
					if ($log_type==4){
						print "Daily HVAC System Statistics</h4></center></td>";
					}else if ($log_type==3){
						print "Weekly HVAC System Statistics</h4></center></td>";
					}else if ($log_type==2){
						print "Monthly HVAC System Statistics</h4></center></td>";
					}else if ($log_type==1){
						print "Yearly HVAC System Statistics</h4></center></td>";
					}
				print "</tr>
				<tr>
					<td align=\"right\">Low Cool Cycles:</td>
					<td width=\"5%\" align=\"center\">".$low_cool_log_count["COUNT(duration)"]."</td>
					<td align=\"right\">Low Cool Cycle Average Duration [Minutes]: </td><td width=\"6%\" align=\"center\">";
					print round($low_cool_log_average_duration["AVG(duration)"]/60000,2);
					print "</td>
				</tr>
				<tr>
					<td align=\"right\">High Cool Cycles:</td>
					<td width=\"5%\" align=\"center\">".$high_cool_log_count["COUNT(duration)"]."</td>
					<td align=\"right\">High Cool Cycle Average Duration [Minutes]: </td><td width=\"6%\" align=\"center\">";
					print round($high_cool_log_average_duration["AVG(duration)"]/60000,2);
					print "</td>
				</tr>
				<tr>
					<td align=\"right\">Low Heat Cycles:</td>
					<td width=\"5%\" align=\"center\">".$low_heat_log_count["COUNT(duration)"]."</td>
					<td align=\"right\">Low Heat Cycle Average Duration [Minutes]: </td><td width=\"6%\" align=\"center\">";
					print round($low_heat_log_average_duration["AVG(duration)"]/60000,2);
					print "</td>
				</tr>
				<tr>
					<td align=\"right\">High Heat Cycles:</td>
					<td width=\"5%\" align=\"center\">".$high_heat_log_count["COUNT(duration)"]."</td>
					<td align=\"right\">High Heat Cycle Average Duration [Minutes]: </td><td width=\"6%\" align=\"center\">";
					print round($high_heat_log_average_duration["AVG(duration)"]/60000,2);
					print "</td>
				</tr>
				<tr>
					<td align=\"right\">Dehumidifier Cycles:</td>
					<td width=\"5%\" align=\"center\">".$dehumidifier_log_count["COUNT(duration)"]."</td>
					<td align=\"right\">Dehumidifier Cycle Average Duration [Minutes]: </td><td width=\"6%\" align=\"center\">";
					print round($dehumidifier_log_average_duration["AVG(duration)"]/60000,2);
					print "</td>
				</tr>
				<tr>
					<td align=\"right\">Humidifier Cycles:</td>
					<td width=\"5%\" align=\"center\">".$humidifier_log_count["COUNT(duration)"]."</td>
					<td align=\"right\">Humidifier Cycle Average Duration [Minutes]: </td><td width=\"6%\" align=\"center\">";
					print round($humidifier_log_average_duration["AVG(duration)"]/60000,2);
					print "</td>
				</tr>
				<tr>
					<td align=\"right\">HVAC Fan Cycles:</td>
					<td width=\"5%\" align=\"center\">".$fan_log_count["COUNT(duration)"]."</td>
					<td align=\"right\">HVAC Fan Cycle Average Duration [Minutes]: </td><td width=\"6%\" align=\"center\">";
					print round($fan_log_average_duration["AVG(duration)"]/60000,2);
					print "</td>
				</tr>
			</table>";
		if ($log_type==4){//Daily
			print "<table border=\"1\" cellspacing=\"1\" cellpadding=\"1\">
				<tr>
					<td colspan=\"4\"><center><h4>HVAC System Statistics</h4></center></td>
				</tr>
				<tr>
					<td><h5>Cycle #</h5></td>
					<td><h5>Cycle Ended</h5></td>
					<td><h5>Duration [Minutes]</h5></td>
				</tr>";
				$x=0;
				if ($low_cool_log_count["COUNT(duration)"]>0){
					$x=0;
					print "<tr>
						<td colspan=\"3\"><center><h4>Low Cool</h4></center></td>
					</tr>";
					 while($row = $sql_low_cool_log->fetch()) {
						 $x++;
						echo "<tr><td align=\"center\">";
						echo $x;
						echo "<td align=\"center\">";
						print $row["timeStamp"];
						echo "</td><td align=\"center\">";
						echo round($row["duration"]/60000,2);
						echo "</td></tr>";
					}
				}
				if ($high_cool_log_count["COUNT(duration)"]>0){
					$x=0;
					print "<tr>
						<td colspan=\"4\"><center><h4>High Cool</h4></center></td>
					</tr>";
					 while($row = $sql_high_cool_log->fetch()) {
						  $x++;
						echo "<tr><td align=\"center\">";
						echo $x;
						echo "<td align=\"center\">";
						echo $row["timeStamp"];
						echo "</td><td align=\"center\">";
						echo round($row["duration"]/60000,2);
						echo "</td></tr>";
					}
				}
				if ($low_heat_log_count["COUNT(duration)"]>0){
					$x=0;
					print "<tr>
						<td colspan=\"4\"><center><h4>Low Heat</h4></center></td>
					</tr>";
					 while($row = $sql_low_heat_log->fetch()) {
						  $x++;
						echo "<tr><td align=\"center\">";
						echo $x;
						echo "<td align=\"center\">";
						echo $row["timeStamp"];
						echo "</td><td align=\"center\">";
						echo round($row["duration"]/60000,2);
						echo "</td></tr>";
					}
				}
				if ($high_heat_log_count["COUNT(duration)"]>0){
					$x=0;
					print "<tr>
						<td colspan=\"4\"><center><h4>High Heat</h4></center></td>
					</tr>";
					 while($row = $sql_high_heat_log->fetch()) {
						 $x++;
						echo "<tr><td align=\"center\">";
						echo $x;
						echo "<td align=\"center\">";
						echo $row["timeStamp"];
						echo "</td><td align=\"center\">";
						echo round($row["duration"]/60000,2);
						echo "</td></tr>";
					}
				}
				if ($dehumidifier_log_count  ["COUNT(duration)"]>0){
					$x=0;
					print "<tr>
						<td colspan=\"4\"><center><h4>Dehumidifier</h4></center></td>
					</tr>";
					 while($row = $sql_dehumidifier_log->fetch()) {
						 $x++;
						echo "<tr><td align=\"center\">";
						echo $x;
						echo "<td align=\"center\">";
						echo $row["timeStamp"];
						echo "</td><td align=\"center\">";
						echo round($row["duration"]/60000,2);
						echo "</td></tr>";
					}
				}
				if ($humidifier_log_count  ["COUNT(duration)"]>0){
					$x=0;
					print "<tr>
						<td colspan=\"4\"><center><h4>Humidifier</h4></center></td>
					</tr>";
					 while($row = $sql_humidifier_log->fetch()) {
						 $x++;
						echo "<tr><td align=\"center\">";
						echo $x;
						echo "<td align=\"center\">";
						echo $row["timeStamp"];
						echo "</td><td align=\"center\">";
						echo round($row["duration"]/60000,2);
						echo "</td></tr>";
					}
				}
				if ($fan_log_count ["COUNT(duration)"]>0){
					$x=0;
					print "<tr>
						<td colspan=\"4\"><center><h4>HVAC Fan</h4></center></td>
					</tr>";
					 while($row = $sql_fan_log->fetch()) {
						 $x++;
						echo "<tr><td align=\"center\">";
						echo $x;
						echo "<td align=\"center\">";
						echo $row["timeStamp"];
						echo "</td><td align=\"center\">";
						echo round($row["duration"]/60000,2);
						echo "</td></tr>";
					}
				}
			print "</table>";
		}
					
		print "</center>";
				


//$conn->close();
$pdo=null; //close the connection
}
?>