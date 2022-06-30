/*
 * EEPROM Write
 *
 * Stores values read from analog input 0 into the EEPROM.
 * These values will stay in the EEPROM when the board is
 * turned off and may be retrieved later by another sketch.
 */

#include <EEPROM.h>
#include <avr/io.h>
#include <avr/pgmspace.h>

// the current address in the EEPROM (i.e. which byte
// we're going to write to next)
#define INCSGDTONEEPROMADDR 1
#define CSGDTOFFEEPROMADDR 2
#define CSGNTONEEPROMADDR 3
#define CSGNOFFEEPROMADDR 4
#define MSGDTONEEPROMADDR 5
#define MSGDTOFFEEPROMADDR 6
#define MSGNTONEEPROMADDR 7
#define MSGNTOFFEEPROMADDR 8
#define HSGDTONEEPROMADDR 9
#define HSGDTOFFEEPROMADDR 10
#define HSGNTONEEPROMADDR 11
#define HSGNTOFFEEPROMADDR 12
#define AADTONEEPROMADDR 13
#define AADTOFFEEPROMADDR 14
#define AANTONEEPROMADDR 15
#define AANTOFFEEPROMADDR 16
#define DTHUMONEEPROMADDR 17
#define DTHUMOFFEEPROMADDR 18
#define NTHUMONEEPROMADDR 19
#define NTHUMOFFEEPROMADDR 20
#define UVLIGHTONHOUREEPROMADDR 21
#define UVLIGHTONMINUTEEEPROMADDR 22
#define UVLIGHTONSECONDEEPROMADDR 23
#define UVLIGHTOFFHOUREEPROMADDR 24
#define UVLIGHTOFFMINUTEEEPROMADDR 25
#define UVLIGHTOFFSECONDEEPROMADDR 26
#define TEMPSCALEEEPROMADDR 27
#define TIMEZONEEEPROMADDR 28
#define TIMEZONEEEPROMADDRSIGN 29
#define LOCALIPADDREEPROMADDRPART1 30
#define LOCALIPADDREEPROMADDRPART2 31
#define LOCALIPADDREEPROMADDRPART3 32
#define LOCALIPADDREEPROMADDRPART4 33
#define SUBNETMASKEEPROMADDRPART1 34
#define SUBNETMASKEEPROMADDRPART2 35
#define SUBNETMASKEEPROMADDRPART3 36
#define SUBNETMASKEEPROMADDRPART4 37
#define GATEWAYEEPROMADDRPART1 38
#define GATEWAYEEPROMADDRPART2 39
#define GATEWAYEEPROMADDRPART3 40
#define GATEWAYEEPROMADDRPART4 41
#define DNSSERVEREEPROMADDRPART1 42
#define DNSSERVEREEPROMADDRPART2 43
#define DNSSERVEREEPROMADDRPART3 44
#define DNSSERVEREEPROMADDRPART4 45
#define USEDHCPEEPROMADDR 46
byte updated = 0;
void setup()
{
  Serial.begin(9600);
}

void loop()
{
  
  if (updated == 0){
    EEPROM.write(INCSGDTONEEPROMADDR, 80);
    EEPROM.write(CSGDTOFFEEPROMADDR, 81);
    EEPROM.write(CSGNTONEEPROMADDR, 82);
    EEPROM.write(CSGNOFFEEPROMADDR, 83);
    EEPROM.write(MSGDTONEEPROMADDR, 84);
    EEPROM.write(MSGDTOFFEEPROMADDR, 85);
    EEPROM.write(MSGNTONEEPROMADDR, 86);
    EEPROM.write(MSGNTOFFEEPROMADDR, 87);
    EEPROM.write(HSGDTONEEPROMADDR, 88);
    EEPROM.write(HSGDTOFFEEPROMADDR, 89);
    EEPROM.write(HSGNTONEEPROMADDR, 90);
    EEPROM.write(HSGNTOFFEEPROMADDR, 91);
    EEPROM.write(AADTONEEPROMADDR, 92);
    EEPROM.write(AADTOFFEEPROMADDR, 93);
    EEPROM.write(AANTONEEPROMADDR, 94);
    EEPROM.write(AANTOFFEEPROMADDR, 95);
    EEPROM.write(DTHUMONEEPROMADDR, 40);
    EEPROM.write(DTHUMOFFEEPROMADDR, 50);
    EEPROM.write(NTHUMONEEPROMADDR, 40);
    EEPROM.write(NTHUMOFFEEPROMADDR, 50);
    EEPROM.write(UVLIGHTONHOUREEPROMADDR, 7);
    EEPROM.write(UVLIGHTONMINUTEEEPROMADDR, 0);
    EEPROM.write(UVLIGHTONSECONDEEPROMADDR, 0);
    EEPROM.write(UVLIGHTOFFHOUREEPROMADDR, 19);
    EEPROM.write(UVLIGHTOFFMINUTEEEPROMADDR, 0);
    EEPROM.write(UVLIGHTOFFSECONDEEPROMADDR, 0);
    EEPROM.write(TEMPSCALEEEPROMADDR, 1);
    EEPROM.write(TIMEZONEEEPROMADDR, 6);
    EEPROM.write(TIMEZONEEEPROMADDRSIGN, 1);
    EEPROM.write(LOCALIPADDREEPROMADDRPART1, 192);
    EEPROM.write(LOCALIPADDREEPROMADDRPART2, 168);
    EEPROM.write(LOCALIPADDREEPROMADDRPART3, 1);
    EEPROM.write(LOCALIPADDREEPROMADDRPART4, 40);
    EEPROM.write(SUBNETMASKEEPROMADDRPART1, 255);
    EEPROM.write(SUBNETMASKEEPROMADDRPART2, 255);
    EEPROM.write(SUBNETMASKEEPROMADDRPART3, 255);
    EEPROM.write(SUBNETMASKEEPROMADDRPART4, 0);
    EEPROM.write(GATEWAYEEPROMADDRPART1, 192);
    EEPROM.write(GATEWAYEEPROMADDRPART2, 168);
    EEPROM.write(GATEWAYEEPROMADDRPART3, 1);
    EEPROM.write(GATEWAYEEPROMADDRPART4, 1);
    EEPROM.write(DNSSERVEREEPROMADDRPART1, 8);
    EEPROM.write(DNSSERVEREEPROMADDRPART2, 8);
    EEPROM.write(DNSSERVEREEPROMADDRPART3, 8);
    EEPROM.write(DNSSERVEREEPROMADDRPART4, 8);
    EEPROM.write(USEDHCPEEPROMADDR, 0);
    
    Serial.print(F("Cold Side Ground Day Time On Temperature has been set to: "));
    Serial.println(EEPROM.read(INCSGDTONEEPROMADDR)); 
    Serial.print(F("Cold Side Ground Day Time Off Temperature has been set to: "));
    Serial.println(EEPROM.read(CSGDTOFFEEPROMADDR));
    Serial.print(F("Cold Side Ground Night Time On Temperature has been set to: "));
    Serial.println(EEPROM.read(CSGNTONEEPROMADDR));
    Serial.print(F("Cold Side Ground Night Time Off Temperature has been set to: "));
    Serial.println(EEPROM.read(CSGNOFFEEPROMADDR));
    Serial.print(F("Middle Side Ground Day Time On Temperature has been set to: "));
    Serial.println(EEPROM.read(MSGDTONEEPROMADDR));
    Serial.print(F("Middle Side Ground Day Time Off Temperature has been set to: "));
    Serial.println(EEPROM.read(MSGDTOFFEEPROMADDR));
    Serial.print(F("Middle Side Ground Night Time On Temperature has been set to: "));
    Serial.println(EEPROM.read(MSGNTONEEPROMADDR));
    Serial.print(F("Middle Side Ground Night Time Off Temperature has been set to: "));
    Serial.println(EEPROM.read(MSGNTOFFEEPROMADDR));
    Serial.print(F("Hot Side Ground Day Time On Temperature has been set to: "));
    Serial.println(EEPROM.read(HSGDTONEEPROMADDR));
    Serial.print(F("Hot Side Ground Day Time Off Temperature has been set to: "));
    Serial.println(EEPROM.read(HSGDTOFFEEPROMADDR));
    Serial.print(F("Hot Side Ground Night Time On Temperature has been set to: "));
    Serial.println(EEPROM.read(HSGNTONEEPROMADDR));
    Serial.print(F("Hot Side Ground Night Time Off Temperature has been set to: "));
    Serial.println(EEPROM.read(HSGNTOFFEEPROMADDR));
    Serial.print(F("Average Ambient Day Time On Temperature has been set to: "));
    Serial.println(EEPROM.read(AADTONEEPROMADDR));
    Serial.print(F("Average Ambient Day Time Off Temperature has been set to: "));
    Serial.println(EEPROM.read(AADTOFFEEPROMADDR));
    Serial.print(F("Average Ambient Night Time On Temperature has been set to: "));
    Serial.println(EEPROM.read(AANTONEEPROMADDR));
    Serial.print(F("Average Ambient Night Time Off Temperature has been set to: "));
    Serial.println(EEPROM.read(AANTOFFEEPROMADDR));
    Serial.print(F("Day Time On Humidity has been set to: "));
    Serial.println(EEPROM.read(DTHUMONEEPROMADDR));
    Serial.print(F("Day Time Off Humidity has been set to: "));
    Serial.println(EEPROM.read(DTHUMOFFEEPROMADDR));
    Serial.print(F("Night Time On Humidity has been set to: "));
    Serial.println(EEPROM.read(NTHUMONEEPROMADDR));
    Serial.print(F("Night Time Off Humidity has been set to: "));
    Serial.println(EEPROM.read(NTHUMOFFEEPROMADDR));
    Serial.print(F("The UV Light On Time has been set to: "));
    Serial.print(EEPROM.read(UVLIGHTONHOUREEPROMADDR));
    Serial.print(F(":"));
    Serial.print(EEPROM.read(UVLIGHTONMINUTEEEPROMADDR));
    Serial.print(F(":"));
    Serial.println(EEPROM.read(UVLIGHTONSECONDEEPROMADDR));
    Serial.print(F("The UV Light Off Time has been set to: "));
    Serial.print(EEPROM.read(UVLIGHTOFFHOUREEPROMADDR));
    Serial.print(F(":"));
    Serial.print(EEPROM.read(UVLIGHTOFFMINUTEEEPROMADDR));
    Serial.print(F(":"));
    Serial.println(EEPROM.read(UVLIGHTOFFSECONDEEPROMADDR));
    Serial.print(F("The Temp Scale has been set to: "));
      if (EEPROM.read(TEMPSCALEEEPROMADDR) == 1){
        Serial.println(F("Fahrenheit"));
      }else{
        Serial.println(F("Celsius"));
      }
    Serial.print(F("The Time Zone has been set to: "));
      if (EEPROM.read(TIMEZONEEEPROMADDRSIGN) == 1){
        Serial.println(EEPROM.read(TIMEZONEEEPROMADDR)*-1);
      }else{
        Serial.println(EEPROM.read(TIMEZONEEEPROMADDR));
      }
    Serial.print(F("The local IP has been set to: "));
    Serial.print(EEPROM.read(LOCALIPADDREEPROMADDRPART1));
    Serial.print(F("."));
    Serial.print(EEPROM.read(LOCALIPADDREEPROMADDRPART2));
    Serial.print(F("."));
    Serial.print(EEPROM.read(LOCALIPADDREEPROMADDRPART3));
    Serial.print(F("."));
    Serial.println(EEPROM.read(LOCALIPADDREEPROMADDRPART4));
    Serial.print(F("The Subnet Mask has been set to: "));
    Serial.print(EEPROM.read(SUBNETMASKEEPROMADDRPART1));
    Serial.print(F("."));
    Serial.print(EEPROM.read(SUBNETMASKEEPROMADDRPART2));
    Serial.print(F("."));
    Serial.print(EEPROM.read(SUBNETMASKEEPROMADDRPART3));
    Serial.print(F("."));
    Serial.println(EEPROM.read(SUBNETMASKEEPROMADDRPART4));
    Serial.print(F("The default gateway has been set to: "));
    Serial.print(EEPROM.read(GATEWAYEEPROMADDRPART1));
    Serial.print(F("."));
    Serial.print(EEPROM.read(GATEWAYEEPROMADDRPART2));
    Serial.print(F("."));
    Serial.print(EEPROM.read(GATEWAYEEPROMADDRPART3));
    Serial.print(F("."));
    Serial.println(EEPROM.read(GATEWAYEEPROMADDRPART4));
    Serial.print(F("The DNS Server has been set to: "));
    Serial.print(EEPROM.read(DNSSERVEREEPROMADDRPART1));
    Serial.print(F("."));
    Serial.print(EEPROM.read(DNSSERVEREEPROMADDRPART2));
    Serial.print(F("."));
    Serial.print(EEPROM.read(DNSSERVEREEPROMADDRPART3));
    Serial.print(F("."));
    Serial.println(EEPROM.read(DNSSERVEREEPROMADDRPART4));
    if (EEPROM.read(USEDHCPEEPROMADDR)==1){
      Serial.print(F("The System is set to use DHCP"));
    }else{
      Serial.print(F("The System is set to NOT use DHCP but static setting"));
    }
    updated = 1;
  }
  
}
