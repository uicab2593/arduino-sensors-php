<?php
require 'vendor/autoload.php';

$sensor = intval($_POST['sensor']);
$val = intval($_POST['value']);

$serial = new PhpSerial;
$serial->deviceSet("/dev/ttyACM0");
$serial->confBaudRate(57600);
$serial->confParity("none");
$serial->confCharacterLength(10);
$serial->confStopBits(1);
$serial->confFlowControl("none");
$serial->deviceOpen();

$byte = $val<<8;
$byte|=$sensor;

$serial->sendMessage($byte);
$serial->deviceClose();