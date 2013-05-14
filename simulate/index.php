<?php

require 'vendor/autoload.php';

$curl = new Curl();

$curl->post('http://requestb.in/ndf5oynd', ['test'=> 5]);
