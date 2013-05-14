<?php

$checkin = json_decode($_POST['checkin']);
file_put_contents('checkins/' . $checkin->venue->name . '.json', $_POST['checkin']);

$user = json_decode($_POST['user']);
file_put_contents('users/' . $user->firstName . '.json', $_POST['user']);

file_put_contents('data.json', json_encode($_POST, JSON_PRETTY_PRINT));