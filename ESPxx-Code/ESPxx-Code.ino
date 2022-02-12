#include <Arduino.h>
#include <ESP8266WiFi.h>
#include <ESP8266WiFiMulti.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <SPI.h>
#include <SD.h>
#include <Arduino_JSON.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

LiquidCrystal_I2C lcd(0x27, 16, 4);
File myFile;
ESP8266WiFiMulti WiFiMulti;

int pres, pos = 1, menuSize = 6, oldPos; //Variable for scrolling on lcd display
int total, lineLength = 54, slot = 0;//variables for file line length and number of total created attendance
int e2e[] = {0, 0, 0};//end to end time array array[0] = start time array[1] = end time array[2]= penalty minute
String configs[4]; //4 element array for array[0] = id,array[1] = label of time, 
//bool status[2];//array[0] = status of day & array[1] = checking mode
String today = "tuesday", now = "20:42"; //Time parameters which should replaced with RTC(real time clock circuit)
int reference, toggler = 0; //Toggling of status to conduct attendance 0=>off 1=>on 2=>penality time
String attList = "test1.txt";//file name for attendants list
String totalAts = "Total.txt";// to reserve number of total attendance created
String configur = "config.txt";//File for configuration of administrator

String menu[] = { //Menu to display on LCD display
  "MENU",
  "Downlink",
  "Uplink",
  "Analysis",
  "Re-config",
  "Clear data",
};

void setup() {
  Serial.begin(115200);
  lcd.init();
  lcd.backlight();
  if (!SD.begin(SS)) {
    Serial.print("Initialization failed!\n");
    lcd.print("Initialization failed!");
    while (1);
  }
  WiFi.mode(WIFI_STA);
  WiFiMulti.addAP("Nesren", "12345665");
  Serial.print("Initialization done.\n");
  Serial.println(dOut(attList));

  reference = calculateTime(now); //Reference time from now calculator
  setNextTime();//Set time for next task start time and ending time
  Serial.printf("Start %d End %d P = %d", e2e[0], e2e[1], e2e[2]);
  Serial.printf(" Today status %s", configs[3]);
  Serial.print(dOut(attList));
  totalAtt(); // Assigns total attendance created to total variable
  menuUpdate(pos); // Display Menu on the lcd screen
}

void loop() {
  int second = millis() / 1000;
  int minute = second / 60;
  second = second % 60;
  int button = analogRead(A0);// Read which button is selected to scroll on the screen
  //Checking if the time is reached or not? if reached make toggler = 1
  if ((reference + minute) == e2e[0]) {
    if (toggler == 0) {
      createAtt();
      toggler = 1;
      lcd.clear();
      lcd.print(configs[1]);
    }
  }
  //Checking if the penalty time is reached or not? if reached make toggler = 2
  else if ((reference + minute) == e2e[1]) {
    if (toggler == 1) {
      lcd.clear();
      lcd.print("Att closing...");
      lcd.setCursor(0, 1);
      lcd.print("Att with penalty time");
      Serial.print("Normal attendance time closing...");
      Serial.println(" Attendance with penalty time");
      toggler = 2;
    }
  }
  //Checking if the time is gone or not? if reached make toggler = 0
  else if ((reference + minute) == (e2e[1] + e2e[2])) {
    if (toggler == 2) {
      lcd.clear();
      lcd.print("Att time end...");
      Serial.println("Att time end...");
      toggler = 0;
      delay(3000);
      slot += 1;
      setNextTime();
      Serial.printf("Next attendance Start %d End %d P = %d", e2e[0], e2e[1], e2e[2]);
      menuUpdate(pos);
    }
  }
  String id = "";
  String cmd = ""; int len;
  //Input data from serial port scanner
  while (Serial.available() > 0) {
    cmd = Serial.readString();
  }
  if (cmd != "") {
    cmd.trim();
    len = cmd.length();
    id = cmd;
    Serial.printf("Data entered: %s Len = %d\n", cmd, len);
  }
  ///////// Processing the button and cursor if button is pressed  /////////////////////////////////////////////////////
  //Checking analog data range for which button is pressed
  if (button > 650 && button < 900) {// If Ok is pressed
    oldPos = pos;
    if (pos == 2) {//Checking the cursor position after OK button is pressed
      lcd.clear();
      download("/attendants1.txt", attList);
    }
    else if(pos == 3){
      lcd.clear();
      uplink();
    }
    else if (pos == 5) {
       download("/config/1", configur);
    }
    else if (pos == 6) {
       clearAll();
    }
    else if (pos == 8 ) {
      lcd.clear();
      lcd.print(menu[pos]);
    }
    else {
      lcd.clear();
      lcd.print(menu[pos-1]);
    }
    delay(240);
  }
  //Button value checking to scroll up
  else if (button > 250 && button < 400) {
    pres = 1;
    menuUpdate(possitioner(pres));
    delay(240);
  }
  //Button value checking to scroll down
  else if (button > 1010) {
    pres = 2;
    menuUpdate(possitioner(pres));
    delay(240);
  }
  if(cmd == "print att"){
    Serial.print(dOut(attList));
  }
  else if(cmd == "show config"){
    Serial.print(dOut(configur));
  }
  else if(cmd == "clear att"){
    clearAll();
  }
  else if(cmd == "uplink"){
    uplink();
  }
  else if(cmd == "download"){
    download("/attendants1.txt", attList);
  }
  else if(cmd == "re-config"){
    download("/config/1", configur);
  }
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////   Operation on input data block, where data is id///////////////////////////////////////////////////
  if (len == 10 || len == 5) {// Is the data is valid id card
    cmd = cmd.substring(0,5);
//    if (configs[4] == "true") { // If attendance is not needed, only presence checking mode
//      takeAtt( cmd );
//    }
//    else 
    if ((toggler == 1 || toggler == 2) && configs[0] ) { //If time is valid time to take attendance 
      takeAtt( cmd );//Take attendance function will be executed with input data
    }
    else {
      lcd.clear();
      lcd.print("Out of time => ");
      Serial.println("Out of time => ");
    }
  }
}
////////////// Functions implementation /////////////////////////////////////////////////////////////////////////
//Minute to to hour add minute converter////////////////////////////// //////////////////////////////////////////
void minuteToTime(int minute) {
  int hour = minute / 60;
  minute = minute % 60;
  Serial.printf("Time is %d : %d \n", hour, minute);
}
/////// String of time to integer minute ///////////////////////////////////////////////////////////////////////
int calculateTime(String now) {
  int hour = (now.substring(0, 2)).toInt();
  int minute = (now.substring(3)).toInt();
  return hour * 60 + minute ;
}
/////// Creates one logical sheet of attendance ///////////////////////////////////////////////////////////////
void createAtt() {
  total++;
  myFile = SD.open(totalAts, sdfat::O_WRITE);
  myFile.print(total);
  myFile.close();
  Serial.printf("Attendance created: Total = %d\n", total);
}
///// Fingerprint scanner, returns id of fingerprint /////////////////////////////////////////////////////////////
String finger() {
  lcd.clear();
  lcd.print("fingerprint data?");
  Serial.println("Scann your fingerprint ....");
  int t = 9;
  while (!Serial.available() && t > 0) {
    lcd.setCursor(0, 3);
    lcd.print(t);
    Serial.print(t);
    t = t - 1;
    delay(1100);
  }
  String nextData = Serial.readString();
  nextData.trim();
  return nextData;
}
/*  The attendance taker function //////////////////////////////////////////////////////////////////////////////
 *  If this function is called with valid data, first it try to find the address of the id
 *  Then from that address full data of a particular id is readed. After that It will check if
 *  this instance of attendance is not taken by that id,means that someone is not trying to 
 *  repeat twice. 
 *  Check if second data is needed to verify e.g the fingerprint? If enabled fingerprint sc-
 *  anner will be called.
 *  Then it will update the attendance field if all things are matched. Otherwise it returns
 *  with an error message.
 *   */
void takeAtt(String id) {
  int loc = findLocation(id);
  String x = ""; float attend;
  if (loc != -1) {
    x = readLine(loc);
    if (x != "") {
      String y = x.substring(9, 13);
      String att = x.substring(5, 9);
//      if (configs[3] == "1") {//Only checking is needed
//        lcd.clear();
//        lcd.print(x.substring(20));
//        lcd.setCursor(0, 1);
//        lcd.print("Found!");
//        return;
//      }
      Serial.print(att);
      //      Serial.printf(" float %f \n", att.toFloat());
      Serial.print(x.substring(20));
      if (y.toInt() < total) {
        String next = x.substring(13, 19);
        next.trim();
        lcd.setCursor(0, 1);
        lcd.print("Dear " + x.substring(20));
        JSONVar data  = JSON.parse(dOut(configur));
        data = JSON.parse((const char *)data["data"]);
        
        if ((bool)data["barcode"] && (bool)data["fingerprint"]) {
          if (next == finger()) {
            if (toggler == 2) {
              attend = ((att.toFloat() + 0.5) / total) * 100;
              updateLine(loc, att.toFloat() + 0.5);
            }
            else {
              attend = ((att.toFloat() + 1.0) / total) * 100;
              updateLine(loc, att.toFloat() + 1);
            }
            lcd.clear();
            lcd.printf(" Attend = %.2f", attend);
            lcd.println("%");
            Serial.printf(" Attend = %.2f", attend);
            Serial.println("%");
          }
          else {
            lcd.clear();
            lcd.print("sorry we didn't find your data");
            Serial.println("sorry we didn't find your data");
          }
        }
        else {
          if (toggler == 2) {
            attend = ((att.toFloat() + 0.5) / total) * 100;
            updateLine(loc, att.toFloat() + 0.5);
          }
          else {
            attend = ((att.toFloat() + 1.0) / total) * 100;
            updateLine(loc, att.toFloat() + 1);
          }
          lcd.clear();
          lcd.printf(" Attend = %.2f", attend);
          lcd.println("%");
          Serial.printf(" Attend = %.2f", attend);
          Serial.println("%");
        }
      }
      else {
        lcd.clear();
        attend = ((att.toInt() + 0.0) / total) * 100;
        lcd.printf(" Already Attended: %.2f", attend);
        lcd.println("%");
        Serial.printf(" Already Attended: %.2f", attend);
        Serial.println("%");
      }
    }
  }
  else {
    Serial.print("Unregistered id");
    lcd.clear();
    lcd.print("Unregistered id");
  }
}
/// Assigns total attendance taken before to total ///////////////////////////////////////////////////////////////
void totalAtt() {
  myFile = SD.open(totalAts);
  String x = myFile.readString();
  total = x.toInt();
  myFile.close();
}
//// Reads 1 line of string data from the location passed ///////////////////////////////////////////////////////
String readLine(int loc) {
  myFile = SD.open(attList);
  myFile.seek(loc * lineLength);
  String x = myFile.readStringUntil('|');
  myFile.close();
  return x;
}
/// Updates 1 line of string data in the location passed with data passed//////////////////////////////////////////
void updateLine(int loc, float att) {
  myFile = SD.open(attList, FILE_WRITE);
  myFile.seek(loc * lineLength + 6);
  myFile.print(att);
  myFile.seek(loc * lineLength + 10);
  myFile.print(total);
  myFile.close();
}
// Find the location (line number) of id passed.It follows Binary Search Algorithm ////////////////////////////////
int findLocation(String id) {
  myFile = SD.open(attList);
  int line = (myFile.size() + 1) / lineLength;
  int mid, left = 0, right = line;
  mid = (left + right) / 2;
  while (left <= right) {
    myFile.seek(mid * lineLength);
    String data = myFile.readStringUntil(' ');
    if (data.toInt() == id.toInt()) {
      myFile.close();
      return mid;
    }
    else if (id.toInt() < data.toInt()) {
      right = mid - 1;
    }
    else {
      left = mid + 1;
    }
    mid = (left + right) / 2;
  }
  myFile.close();
  return -1;
}
////////////  Print file data from specified file  /////////////////////////////////////////////////////////////////
String dOut(String file) {
  myFile = SD.open(file);
  String data = "";
  if (myFile.available()) {
    data = myFile.readString();
  }
  myFile.close();
  return data;
}
/////////// Clears previously taken attendance data ////////////////////////////////////////////////////////////////
void clearAll() {
  myFile = SD.open(attList, FILE_WRITE);
  int s = myFile.size() + 1;
  s = s / lineLength;
  for (int i = 0; i < s; i++) {
    myFile.seek( i * lineLength + 6);
    myFile.print("0   0   ");
  }
  myFile.close();
  total = -1;
  createAtt();
  Serial.print("All data cleared\n");
}
void saveFile(String fileName, String data) {
  myFile = SD.open(fileName, sdfat::O_WRITE);
  myFile.print(data);
  myFile.close();
}
///////////Upload data from sd card to server /////////////////////////////////////////////////////////////////////
void uplink() {
  myFile = SD.open(attList);
  int s = myFile.size() + 1;
  s = s / lineLength;
  String data = "", x;
  for (int i = 0; i < s; i++) {
    myFile.seek( i * lineLength + 6);
    x = myFile.readStringUntil(' ');
    data += x;
    data += ",";
  }
  myFile.close();
  uploadData(data, "/update/1");
}
/// Download data from specified url to the file passed to the function ///////////////////////////////////////////////
void download(String fromDir, String toFile) {
  if ((WiFiMulti.run() == WL_CONNECTED)) {
    WiFiClient client;
    HTTPClient http;
    Serial.print("[HTTP] begin...\n");
    if (http.begin(client, "192.168.43.187", 8080, fromDir)) {
      Serial.print("[HTTP] GET...\n");
      int httpCode = http.GET();
      if (httpCode > 0) {
        Serial.printf("[HTTP] GET... code: %d\n", httpCode);
        if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
          String payload = http.getString();
          saveFile(toFile, payload);
        }
      }
      else {
        Serial.printf("[HTTP] GET... failed, error: %s\n", http.errorToString(httpCode).c_str());
      }
      http.end();
    } else {
      Serial.printf("[HTTP} Unable to connect\n");
    }
  }
  else {
    Serial.println("Couldn't connected to wifi");
  }
}
//// Updates the menu on the screen if button is pressed ///////////////////////////////////// ///////////////////////
void menuUpdate(int current) {
  lcd.clear();
  int inital = 0, last = 4;
  if (current > 4) {
    inital = current - 4;
    last = current;
  }
  int cursorpos = 0;
  for (int i = inital; i < last; i++) {
    if (current == (i + 1)) {
      lcd.setCursor(1, cursorpos);
      lcd.print(">");
      lcd.print(menu[i]);
    }
    else {
      lcd.setCursor(2, cursorpos);
      lcd.print(menu[i]);
    }
    cursorpos++;
  }
}
/// Posininer to correctly position the cursor while scrolling ///////////////////////////////////////////////////////
int possitioner(int pres) {
  if (pres == 2) {
    pos++;//Down direction
    if ( pos > menuSize ) {
      pos = menuSize;
    }
  }
  else if (pres == 1) {
    pos--;//Up direction
    if (pos < 1) {
      pos = 1;
    }
  }
  return pos;
}
//Set time for next task start time and ending time ///////////////////////////////////////////////////////////////////
void setNextTime() {
  JSONVar myObject = JSON.parse(dOut(configur));
  JSONVar inObject;
//  e2e[0] = 100000;
    slot = slot%2;
//  for (int i = 1; i < 5; i++) {
    inObject = JSON.parse((const char * )myObject["label" + String(slot+1)]);
//    int t = calculateTime((const char * )inObject["start"]);
//    Serial.printf("Comp %d : %d < %d",(slot+1),e2e[0]);
//    if ((x < e2e[0]) && x != 0) {
//      Serial.print(i);
      e2e[0] = calculateTime((const char * )inObject["start"]);
      e2e[1] = calculateTime((const char * )inObject["end"]);
      String x = String((const char * )inObject["penality"]);
      e2e[2] = x.toInt();
      configs[1] = String( (const char *)inObject["name"]);
//    }
//  }
  inObject = JSON.parse((const char *) myObject["day"]);
  configs[0] = String((int)myObject["user_id"]);
  configs[3] = String((bool)inObject[today]);
}
void uploadData(String data, String toUrl){
    if ((WiFiMulti.run() == WL_CONNECTED)) {
    WiFiClient client;
    HTTPClient http;
    Serial.print("[HTTP] begin...\n");
    if (http.begin(client, "http://192.168.43.187:8080/update/1?data="+data+"&totals="+total)) {  // HTTP
      Serial.print("[HTTP] GET...\n");
      // start connection and send HTTP header
      int httpCode = http.GET();
      // httpCode will be negative on error
      if (httpCode > 0) {
        // HTTP header has been send and Server response header has been handled
        Serial.printf("[HTTP] GET... code: %d\n", httpCode);
        // file found at server
        if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
          String payload = http.getString();
          Serial.println(payload);
        }
      } else {
        Serial.printf("[HTTP] GET... failed, error: %s\n", http.errorToString(httpCode).c_str());
      }

      http.end();
    } else {
      Serial.printf("[HTTP} Unable to connect\n");
    }
  }
}
