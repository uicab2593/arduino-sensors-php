<?php
$sensor = 2;
$val = 100;
$byte = $val<<8;
$byte|=$sensor;
echo $byte;