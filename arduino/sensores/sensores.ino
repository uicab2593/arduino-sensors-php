#include <SPI.h>
#include "nRF24L01.h"
#include "RF24.h"
#include "printf.h"
RF24 radio(9,10);
const uint64_t pipes[3] = {0xF0F0F0F0F1LL,0xF0F0F0F0F2LL,0xF0F0F0F0F3LL};
const uint64_t configsPipes[3] = {0xF0F0F0F0F4LL,0xF0F0F0F0F5LL,0xF0F0F0F0F6LL};

const int generalDigitalInput=7;
const int generalAnalogInput=0;
const int generalDigitalOutput=6;
const int idOutput=5;

byte event = 0; //byte que se envia a la central
int currentSensor = 0; //sensor actual
int analogUmbral=100;
void setup(void){
  Serial.begin(57600);
  printf_begin();
  radio.begin();
  radio.setRetries(15,15);
  radio.openReadingPipe(1,configsPipes[0]);
  radio.openWritingPipe(pipes[currentSensor]);
  radio.printDetails();
  radio.startListening();
  pinMode(generalDigitalInput,INPUT);
  pinMode(generalDigitalOutput,OUTPUT);
  pinMode(idOutput,OUTPUT);
}
bool prevState=false;
bool aux;
bool digitalParameter = true;
int readPin;
int prevParameter = true;
void loop(void){
  switch(currentSensor){
    case 0:
      readPin = digitalRead(generalDigitalInput); 
      readPin = digitalParameter?readPin:(readPin==1?0:1);
      if(readPin!=prevState){
        prevState = readPin;
        sendInfo(prevState);
      }
      break;
    case 1:
      readPin = digitalRead(generalDigitalInput); 
      readPin = digitalParameter?readPin:!readPin;
      if(readPin!=prevState){
        prevState = readPin;
        sendInfo(prevState);
      }
      break;
    case 2:
      aux = analogRead(generalAnalogInput)>analogUmbral; 
      if(aux != prevState){
        prevState = aux;
        sendInfo(prevState);
      }
      break;
    default:
      delay(1000);
      break;
  }
  checkConfig();
}
void checkConfig(){
  unsigned long started_waiting_at = millis();
  bool timeout = false;
  while(!radio.available()&&!timeout)
    if(millis()-started_waiting_at>200)
      timeout = true;
  if(!timeout){
    unsigned long data;
    radio.read(&data,sizeof(unsigned long));
    int sensor = data & 255;
    if(sensor==currentSensor){
      int newUmbral = data>>8;
      if(newUmbral==1000){
        digitalWrite(idOutput,HIGH);
        delay(1000);
        digitalWrite(idOutput,LOW);
      }else{
        analogUmbral = newUmbral;
        digitalParameter = analogUmbral>0;
        Serial.print(newUmbral);              
      }
    }
  }
}
void sendInfo(bool sensorState){
  digitalWrite(generalDigitalOutput,sensorState?HIGH:LOW);
  radio.stopListening();
  printf("Enviando sensor: %d, valor: %d ",currentSensor,sensorState);
  event=currentSensor;
  event<<=1;
  event|=sensorState;
  while(!radio.write(&event,sizeof(byte)));
  // bool ok = ;
  // if (ok){
  //   printf("ok...\n\r");
  // }else{
  //   printf("failed.\n\r");
  // }
  radio.startListening();
}
