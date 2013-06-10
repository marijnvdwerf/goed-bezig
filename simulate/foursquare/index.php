<?php
require 'config.php';

$url = "https://foursquare.com/oauth2/authenticate"
     . "?client_id=$client_id"
     . "&response_type=token"
     . "&redirect_uri=$redirect_uri";

header("location: $url");

