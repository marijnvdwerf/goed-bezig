<?php

$checkin = json_decode($_POST['checkin']);
file_put_contents('checkin/' . $checkin->venue->name . '.json', $_POST['checkin']);

$user = json_decode($_POST['user']);
file_put_contents('user/' . $user->firstName . '.json', $_POST['user']);

file_put_contents('data.json', json_encode($_POST, JSON_PRETTY_PRINT));