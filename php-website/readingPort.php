<?php
require 'vendor/autoload.php';

$serial = new PhpSerial;

$serial->deviceSet("/dev/ttyACM0");

$serial->confBaudRate(57600);
$serial->confParity("none");
$serial->confCharacterLength(10);
$serial->confStopBits(1);
$serial->confFlowControl("none");

$serial->deviceOpen();

// ratchet
$context = new ZMQContext();
$socket = $context->getSocket(ZMQ::SOCKET_PUSH,'my pusher');
$socket->connect("tcp://127.0.0.1:5555");
while(true){
	while(($read = $serial->readPort()) == "");
	$socket->send($read);
	echo $read;
}
$serial->deviceClose();