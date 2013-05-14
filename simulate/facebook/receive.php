<?php

  $verify_token = 'goedbezig';

  if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['hub_mode'])
    && $_GET['hub_mode'] == 'subscribe' && isset($_GET['hub_verify_token'])
    && $_GET['hub_verify_token'] == $verify_token) {
      echo $_GET['hub_challenge'];
	  $tekst = file_get_contents('postData.txt');
	$tekst .= "HOI IK BEN GET";
	file_put_contents('postData.txt', $tekst);
	  
  } 
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_body = file_get_contents('php://input');
	file_put_contents('postData.json', $post_body);
	$tekst = file_get_contents('postData.txt');
	$tekst .= "DOET EEN POST ";
	$tekst .= date('Y,m,d,H:i:s');
	file_put_contents('postData.txt', $tekst);
	$obj = json_decode($post_body, true);
	
	
	// $obj will contain the list of fields that have changed
  }