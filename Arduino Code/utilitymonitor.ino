#include <Wire.h>
#include <Adafruit_AM2315.h>
#include <avr/io.h>
#include <avr/pgmspace.h>
#include <Ethernet.h>
#include <EEPROM.h>
#include <Dns.h>

const char* ip_to_str(const uint8_t*);
byte mac[] = {0x00, 0xAA, 0xBB, 0xCC, 0xDE, 0x03 }; 
byte serverip[] = {192, 168, 1, 13};
EthernetClient client;
DNSClient dnClient;
IPAddress healthcheckio_IP;

//temp and humidty sensor variables
bool AM2315_detected=false;
Adafruit_AM2315 am2315;
unsigned long temp_hum_interval=10000;
long interval_heartbeat = 900000;
unsigned long temp_hum_previousMillis;
long previousMillis_heartbeat = 0;

float average_temp[6];
float average_hum[6];
byte average_counter=0;

//store the pin read state data
int water_heater_state = 0;         // variable for reading the pushbutton status
int low_heat_state =0;
int high_heat_state=0;
int low_cool_state=0;
int high_cool_state=0;
int fan_state=0;
int humidifier_state=0;
int dehumidifier_state=0;
int filter_state=0;
int estop_state=0;

//if the pin activates, send a message to the server once and set the flag to prevent message from sending again
bool low_heat_sent_flag =false;
bool high_heat_sent_flag=false;
bool low_cool_sent_flag=false;
bool high_cool_sent_flag=false;
bool fan_sent_flag = false;
bool humidifier_sent_flag=false;
bool dehumidifier_sent_flag=false;
bool filter_sent_flag=false;
bool estop_sent_flag=false;

int water_heater_countdown =0;

//times how long the pin was active so we can send that data to the server when the pin deactivates
unsigned long low_heat_timer=0;
unsigned long high_heat_timer=0;
unsigned long low_cool_timer=0;
unsigned long high_cool_timer=0;
unsigned long fan_timer=0;
unsigned long humidifier_timer=0;
unsigned long dehumidifier_timer=0;
unsigned long filter_timer=0;
unsigned long estop_timer=0;

//which pins are the inputs connected to?
#define water_heater_pin 38
#define low_heat_pin 28
#define high_heat_pin 30
#define low_cool_pin 32
#define high_cool_pin 34
#define fan_pin 26
#define humidifier_pin 36
#define dehumidifier_pin 40
#define filter_pin 24
#define estop_pin 22


#define low_heat_pin_active_level LOW
#define high_heat_pin_active_level LOW
#define low_cool_pin_active_level LOW
#define high_cool_pin_active_level LOW
#define fan_pin_active_level LOW
#define humidifier_pin_active_level LOW
#define dehumidifier_pin_active_level LOW
#define filter_pin_active_level HIGH
#define estop_pin_active_level LOW

unsigned long previousMillis = 0;
unsigned long currentMillis = 0;
unsigned long duration_clac = 0;
long water_heater_interval = 30000; // READING water_heater_interval 30 seconds

void setup() { 
	Serial.begin(115200);
	pinMode(water_heater_pin, INPUT);
	pinMode(low_heat_pin, INPUT);
	pinMode(high_heat_pin, INPUT);
	pinMode(low_cool_pin, INPUT);
	pinMode(high_cool_pin, INPUT);
	pinMode(fan_pin, INPUT);
	pinMode(humidifier_pin, INPUT);
	pinMode(dehumidifier_pin, INPUT);
	pinMode(filter_pin, INPUT);
	pinMode(estop_pin, INPUT);
	if (! am2315.begin()) {
		Serial.println(F("AM2315 Sensor not found, check wiring & pullups!"));
		AM2315_detected=false;
		Serial.print(F("AM2315_detected set to "));
		Serial.println(AM2315_detected);
	}else{
		Serial.println(F("AM2315 Sensor Detected!"));
		AM2315_detected=true;
		Serial.print(F("AM2315_detected set to "));
		Serial.println(AM2315_detected);
		Wire.setClock(31000L);//reset the TWI bus to a slower clock of 31,000 Hz to allow better data transmission over a greater length of cable
		Serial.println(F("wire clock set to 31,000 Hz"));
    delay(5000);
		am2315.readHumidity();
		delay(100);
		am2315.readTemperature();
	}

    Serial.println(F("Initialize Ethernet with DHCP:"));

	if (Ethernet.begin(mac) == 0) {
		Serial.println(F("Failed to configure Ethernet using DHCP"));
		if (Ethernet.hardwareStatus() == EthernetNoHardware) {
			Serial.println(F("Ethernet shield was not found.  Sorry, can't run without hardware. :("));
		} else if (Ethernet.linkStatus() == LinkOFF) {
			Serial.println(F("Ethernet cable is not connected."));
		}
		// no point in carrying on, so do nothing forevermore:
		while (true) {
			delay(10000);
			Serial.println(F("Reset or Reboot the Arduino."));
		}
	}

	// print your local IP address:
	Serial.print("My IP address: ");
	Serial.println(Ethernet.localIP());
  
	dnClient.begin(Ethernet.dnsServerIP());
	if(dnClient.getHostByName("hc-ping.com",healthcheckio_IP) == 1) {
		Serial.print(F("hc-ping.com = "));
		Serial.println(healthcheckio_IP);
		Serial.println("");
		Serial.println("");
	}else{ 
		Serial.print(F("dns lookup failed"));
	}

}

void loop(){
  float temperature, humidity;

	//maintain DHCP IP lease
	switch (Ethernet.maintain()) {
		case 1:
		  //renewed fail
		  Serial.println(F("Error: renewed fail"));
		  break;
		case 2:
		  //renewed success
		  Serial.println(F("Renewed success"));
		  //print your local IP address:
		  Serial.print(F("My IP address: "));
		  Serial.println(Ethernet.localIP());
		  break;
		case 3:
		  //rebind fail
		  Serial.println(F("Error: rebind fail"));
		  break;
		case 4:
		  //rebind success
		  Serial.println(F("Rebind success"));
		  //print your local IP address:
		  Serial.print(F("My IP address: "));
		  Serial.println(Ethernet.localIP());
		  break;
		default:
		  //nothing happened
		  break;
	}
  
	delay(2000);//DEBOUNCE
	//read all pin status
	water_heater_state = digitalRead(water_heater_pin);
    low_heat_state = digitalRead(low_heat_pin);
    high_heat_state = digitalRead(high_heat_pin);
    low_cool_state = digitalRead(low_cool_pin);
    high_cool_state = digitalRead(high_cool_pin);
    fan_state = digitalRead(fan_pin);
    humidifier_state = digitalRead(humidifier_pin);
    dehumidifier_state = digitalRead(dehumidifier_pin);
    filter_state = digitalRead(filter_pin);
    estop_state = digitalRead(estop_pin);
	
	currentMillis = millis();


	//****************************************************************************
	//Start process the temperature and humidity
	//****************************************************************************
	if (AM2315_detected==true){
		if(currentMillis - temp_hum_previousMillis > temp_hum_interval) { // process once per interval
			if (average_counter <6){
        
      if (! am2315.readTemperatureAndHumidity(&temperature, &humidity)) {
        return;
      }
        average_hum[average_counter] = humidity;
        average_temp[average_counter] = (temperature * 1.8 ) + 32.0;
				Serial.print(F("Hum: ")); Serial.println(average_hum[average_counter]);
				Serial.print(F("Temp: ")); Serial.println(average_temp[average_counter]);
				average_counter++;
				temp_hum_previousMillis = currentMillis; 
			}else{
				average_counter=0;
        if (! am2315.readTemperatureAndHumidity(&temperature, &humidity)) {
        return;
      }
        average_hum[average_counter] = humidity;
        average_temp[average_counter] = (temperature * 1.8 ) + 32.0;
				delay(100);
				Serial.print(F("Hum: ")); Serial.println(average_hum[average_counter]);
				Serial.print(F("Temp: ")); Serial.println(average_temp[average_counter]);
				average_counter++;
				temp_hum_previousMillis = currentMillis; 
				Serial.println(F("Logging 1st floor average temperature"));
				Serial.print(F("Average Hum: ")); Serial.println((average_hum[0] + average_hum[1] + average_hum[2] + average_hum[3] + average_hum[4] + average_hum[5])/6);
				Serial.print(F("Average Temp: ")); Serial.println((average_temp[0] + average_temp[1] + average_temp[2] + average_temp[3] + average_temp[4] + average_temp[5])/6);
				if (client.connect(serverip,80)) { 
					Serial.println(F("Client Connected updating 1st floor temperature logs"));
					client.print(F("GET /admin/first_floor_add.php?temp="));
					client.print((average_temp[0] + average_temp[1] + average_temp[2] + average_temp[3] + average_temp[4] + average_temp[5])/6);
					client.print(F("&hum="));
					client.print((average_hum[0] + average_hum[1] + average_hum[2] + average_hum[3] + average_hum[4] + average_hum[5])/6);
					client.println( F(" HTTP/1.1"));
					client.println( F("Host: 192.168.1.13") );
					client.println( F("Content-Type: application/x-www-form-urlencoded") );
					client.println( F("Connection: close") );
					client.println();
					client.println();
					client.println( F("Connection: close") );
					client.println();
					client.println();
					client.println( F("Connection: close") );
					client.println();
					client.println();
					client.stop();
					client.stop();
				} else{
					Serial.println(F("could not connect to server"));
				}
			}
		}
	}



	//****************************************************************************
	//Start process the water heater data
	//****************************************************************************
	if(currentMillis - previousMillis > water_heater_interval) { // READ ONLY ONCE PER water_heater_interval
    	previousMillis = currentMillis;	
		if (water_heater_state == LOW){
			Serial.println(F("Water Heater Pin LOW"));
			Serial.println(F("Logging basic water heater status with Server"));
			if (client.connect(serverip,80)) { // REPLACE WITH YOUR SERVER ADDRESS
				Serial.println(F("Client Connected updating water heater status"));
				client.print(F("GET /admin/waterheatermonitor.php"));
				client.println( F(" HTTP/1.1"));
				client.println( F("Host: 192.168.1.13") );
				client.println( F("Content-Type: application/x-www-form-urlencoded") );
				client.println( F("Connection: close") );
				client.println();
				client.println();
				client.println( F("Connection: close") );
				client.println();
				client.println();
				client.println( F("Connection: close") );
				client.println();
				client.println();
				client.stop();
				client.stop();
			} else{
				Serial.println(F("could not connect to server"));
			}
		}
		if (water_heater_state == HIGH) { //if the NO contact on the leak break detector closes, a leak is present, send data to server
			Serial.println(F("Water Heater Pin HIGH: Sending Email Notification - - LEAK DETECTED!!!!"));
			if (client.connect(serverip,80)) { // REPLACE WITH YOUR SERVER ADDRESS
				Serial.println(F("Client Connected - sending mail for water heater leak!!"));
				client.print(F("GET /admin/waterheatermail.php"));
				client.println( F(" HTTP/1.1"));
				client.println( F("Host: 192.168.1.13") );
				client.println( F("Content-Type: application/x-www-form-urlencoded") );
				client.println( F("Connection: close") );
				client.println();
				client.println();
				client.println( F("Connection: close") );
				client.println();
				client.println();
				client.println( F("Connection: close") );
				client.println();
				client.println();
				client.stop();
				client.stop();
			} else{
				Serial.println(F("could not connect to server"));
			}
		}
	}
	//****************************************************************************
	//END process the water heater data
	//****************************************************************************



	//****************************************************************************
	//Start LOW HEAT data
	//****************************************************************************
	HVAC_process(low_heat_state, low_heat_pin_active_level, "Low Heat", low_heat_sent_flag, low_heat_timer, "low_heat", "low_heat_log.php");

	//****************************************************************************
	//Start HIGH HEAT data
	//****************************************************************************
	HVAC_process(high_heat_state, high_heat_pin_active_level, "High Heat", high_heat_sent_flag, high_heat_timer, "high_heat", "high_heat_log.php");

	//****************************************************************************
	//Start LOW COOL data
	//****************************************************************************
	HVAC_process(low_cool_state, low_cool_pin_active_level, "Low Cool", low_cool_sent_flag, low_cool_timer, "low_cool", "low_cool_log.php");

	//****************************************************************************
	//Start HIGH COOL data
	//****************************************************************************
	HVAC_process(high_cool_state, high_cool_pin_active_level, "High Cool", high_cool_sent_flag, high_cool_timer, "high_cool", "high_cool_log.php");

	//****************************************************************************
	//Start fan data
	//****************************************************************************
	HVAC_process(fan_state, fan_pin_active_level, "HVAC Fan", fan_sent_flag, fan_timer, "fan", "fan_log.php");

	//****************************************************************************
	//Start humidifier data
	//****************************************************************************
	HVAC_process(humidifier_state, humidifier_pin_active_level, "Humidifier", humidifier_sent_flag, humidifier_timer, "humidifier", "humidifier_log.php");


	//****************************************************************************
	//Start dehumidifier data
	//****************************************************************************
	HVAC_process(dehumidifier_state, dehumidifier_pin_active_level, "De-humidifier", dehumidifier_sent_flag, dehumidifier_timer, "dehumidifier", "dehumidifier_log.php");

	//****************************************************************************
	//Start filter data
	//****************************************************************************
	HVAC_process(filter_state, filter_pin_active_level, "HVAC Filter", filter_sent_flag, filter_timer, "filter", "filter_log.php");

	//****************************************************************************
	//Start ESTOP data
	//****************************************************************************
	HVAC_process(estop_state, estop_pin_active_level, "E-Stop", estop_sent_flag, estop_timer, "estop", "estop_log.php");
	//Serial.println();
	//Serial.println(F("__________________________________________"));
	//Serial.println();
	  
	//****************************************************************************
	//HEALTHCHECKS.IO ping processing 
	//****************************************************************************
	if(currentMillis - previousMillis_heartbeat > interval_heartbeat) { // PERFORM ONLY ONCE PER INTERVAL
		previousMillis_heartbeat = currentMillis; 
		if(dnClient.getHostByName("hc-ping.com",healthcheckio_IP) == 1) {
			Serial.print(F("hc-ping.com = "));
			Serial.println(healthcheckio_IP);
		}else{
			Serial.print(F("dns lookup failed"));
		}

		if (client.connect(healthcheckio_IP,80)) {
			Serial.println(F("Heartbeat Client Connected"));
			Serial.println("");
			client.println(F("GET /xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx HTTP/1.1"));
			client.println(F("Host: hc-ping.com"));
			client.println(F("Connection: close"));
			client.println();
			client.stop();
		} else{
			Serial.print(F("Heartbeat could not connect to server"));
		}
	}
}

// Just a utility function to nicely format an IP address.
const char* ip_to_str(const uint8_t* ipAddr)
{
	static char buf[16];
	sprintf(buf, "%d.%d.%d.%d\0", ipAddr[0], ipAddr[1], ipAddr[2], ipAddr[3]);
	return buf;
}


void HVAC_process(int state, int pin_active_level, const char* message, bool &sent_flag, unsigned long &timer, const char* sql_column, const char* log_php_file){
	//for "sql_column" acceptable inputs are "fan", "low_cool", "high_cool", "low_heat", "high_heat", "humidifier", "dehumidifier" without the quotes as those are the column names in the SQL server table
	if (state == pin_active_level){ //stage active
		//Serial.print(message);
		//Serial.println(F(" Pin Active"));
		if (sent_flag ==false){ //Have not updated server about the pin state yet
			Serial.print(F("Sending "));
			Serial.print(message);
			Serial.println(F(" Pin Active State to server"));
			timer = millis(); //record time when pin went active
      
			//send message to server that the pin has become active
			if (client.connect(serverip,80)) { // 
				Serial.print(F("Client Connected - "));
				Serial.print(message);
				Serial.println(F(" state set to 1"));
				client.print(F("GET /admin/hvac/state.php?column="));
				client.print(sql_column);
				client.print(F("&state=1"));
				client.println( F(" HTTP/1.1"));
				client.println( F("Host: 192.168.1.13") );
				client.println( F("Content-Type: application/x-www-form-urlencoded") );
				client.println( F("Connection: close") );
				client.println();
				client.println();
				client.println( F("Connection: close") );
				client.println();
				client.println();
				client.println( F("Connection: close") );
				client.println();
				client.println();
				client.stop();
				client.stop();
				sent_flag = true; //we sent the message to the server, set the flag so it is not sent again while the pin is active
			} else{
				Serial.println(F("could not connect to server"));
				sent_flag = false;//because the server was not able to be updated, reset the flag so another attempt can be made to update server
			}
		}else{
			//Serial.print(F("Message already sent to server that "));
			//Serial.print(message);
			//Serial.println(F(" pin is active"));
		}
	}else{ //the pin is not active
		//Serial.print(message);
		//Serial.println(F(" Pin is NOT active"));
		if (sent_flag ==true){//was it just previously active and a message sent to the server?
			//time to update the server that the pin is no longer active and to send how long the pin was active for

			//update server about pin status
			if (client.connect(serverip,80)) { 
				Serial.print(F("Client Connected - "));
				Serial.print(message);
				Serial.println(F(" state set to 0"));;
				client.print(F("GET /admin/hvac/state.php?column="));
				client.print(sql_column);
				client.print(F("&state=0"));
				client.println( F(" HTTP/1.1"));
				client.println( F("Host: 192.168.1.13") );
				client.println( F("Content-Type: application/x-www-form-urlencoded") );
				client.println( F("Connection: close") );
				client.println();
				client.println();
				client.println( F("Connection: close") );
				client.println();
				client.println();
				client.println( F("Connection: close") );
				client.println();
				client.println();
				client.stop();
				client.stop();
				sent_flag=false; //reset the message flasg for when the pin becomes active again

			} else{
				Serial.println(F("could not connect to server"));
				sent_flag=true; //reset the message flag because we still need to tell the server the pin has deactivated
			}

			//send length of time the pin was active
			duration_clac = millis() - timer;

			if (client.connect(serverip,80)) { // 
				Serial.print(F("Client Connected saving duration "));
				Serial.print(message);
				Serial.println(F(" was on"));
				client.print(F("GET /admin/hvac/"));
				client.print(log_php_file);
				client.print(F("?duration="));
				client.print(duration_clac);
				client.println( F(" HTTP/1.1"));
				client.println( F("Host: 192.168.1.13") );
				client.println( F("Content-Type: application/x-www-form-urlencoded") );
				client.println( F("Connection: close") );
				client.println();
				client.println();
				client.println( F("Connection: close") );
				client.println();
				client.println();
				client.println( F("Connection: close") );
				client.println();
				client.println();
				client.stop();
				client.stop();
			} else{
				Serial.println(F("could not connect to server"));
			}
		}
	}
}

