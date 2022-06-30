<?php
//requires library: https://github.com/influxdata/influxdb-client-php#install-the-library

//***************************************
//USER VARIABLES
//***************************************
$measurement="House_Temp_Hum";
$email1_file_name="first_floor_mail.php";//when temperature is too low
$email2_file_name="first_floor_mail2.php";//when temperature is too high
$config_file_location="/volume1/web/config/config_files/config_files_local/house_config.txt";


//***************************************
//START OF CODE
//***************************************

error_reporting(E_ALL ^ E_NOTICE); 
$data = file_get_contents("".$config_file_location."");
$pieces = explode(",", $data);
$script_enable=$pieces[1];
$max_temp=$pieces[4];
$min_temp=$pieces[5];
$sensor_name=$pieces[9];
$influxdb_host=$pieces[12];
$influxdb_port=$pieces[13];
$influxdb_name=$pieces[14];
$influxdb_user=$pieces[15];
$influxdb_pass=$pieces[16];
include $_SERVER['DOCUMENT_ROOT']."/functions.php";
$generic_error="";
require $_SERVER['DOCUMENT_ROOT']."/admin/vendor/autoload.php";

use InfluxDB2\Client;
use InfluxDB2\Point;


if ($script_enable==1){


	[$temp, $generic_error] = test_input_processing($_GET['temp'], 0.0, "float", 0.0, 150.0);
	[$hum, $generic_error] = test_input_processing($_GET['hum'], 0.0, "float", 0.0, 100.0);
	
$post_url="".$measurement.",sensor_name=$sensor_name temp=$temp,hum=$hum";


$client = new InfluxDB2\Client(["url" => "http://".$influxdb_host.":".$influxdb_port."", "token" => "".$influxdb_pass."",
    "bucket" => "".$influxdb_name."",
    "org" => "home",
    "precision" => InfluxDB2\Model\WritePrecision::NS
]);
$write_api = $client->createWriteApi();
$write_api->write($post_url);
$write_api->close();
$client->close();

	
	print "Temperature: ";
	print $temp;
	print "Humidity: ";
	print $hum;
	print "<br><br>";
	if ($temp <$min_temp){
		include_once ''.$email1_file_name.'';
		print " The House is too Cold!";
	}else{
		print " Temperature Acceptable";
	}
	
	if ($temp >=$max_temp){
		include_once ''.$email1_file_name2.'';
		print " The House is too HOT!";
	}else{
		print " Temperature Acceptable";
	}
}
?>
