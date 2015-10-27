/*
 Copyright (C) 2011 J. Coliz <maniacbug@ymail.com>

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 version 2 as published by the Free Software Foundation.
 */

#include <SPI.h>
#include "nRF24L01.h"
#include "RF24.h"
#include "printf.h"

RF24 radio(9,10);

const uint64_t pipes[3] = {0xF0F0F0F0F1LL,0xF0F0F0F0F2LL,0xF0F0F0F0F3LL};
const uint64_t configsPipes[3] = {0xF0F0F0F0F4LL,0xF0F0F0F0F5LL,0xF0F0F0F0F6LL};
byte event=0;
String umbralString = "";
void setup(void)
{
	Serial.begin(57600);
	printf_begin();
	radio.begin();
	radio.setRetries(15,15);
	for (int i = 0; i < 3; i++)
	{
		radio.openReadingPipe(i+1,pipes[i]);
	}
	// radio.openWritingPipe(configsPipe);
	radio.startListening();
	// umbralString.reserve(100);
}

void loop(void)
{
	// uint8_t pipe_num;
	if(radio.available())
	{
		bool done = false;
		while (!done)
		{
			 done = radio.read(&event,sizeof(byte));
			 delay(20);
		}
		bool sensorState = 1 & event;
		unsigned int sensor=event>>1;
		printf("%d:%d",sensor,sensorState);
	}
	/* code */
	// recibir configuraciones
	checkConfig();
}
void checkConfig(){
	if(Serial.available()){
    	radio.stopListening();
  		radio.openWritingPipe(configsPipes[0]);
		unsigned long code=Serial.readString().toInt();
	    bool ok = radio.write(&code,sizeof(unsigned long));
			// printf("codigo: %i ",code);
	  //   if (ok){
		 //    printf("ok...\n\r");
		 //  }else{
		 //    printf("failed.\n\r");
		 //  }
	    radio.startListening();
	}
}
