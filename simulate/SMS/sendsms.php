<?php
/*
  =======================================================================
   File:     sendsms.php
   Created:  16-01-2005
   Author:   Mollie B.V.
   Version:  v 2.2 09-03-2009

   More info? Go to http://www.mollie.nl/
  ========================================================================
*/
require('classes/class.mollie.php');
// or php4: require('classes/class.mollie-php4.php');
  $sms = new mollie();

  // Choose SMS gateway
  $sms->setGateway(2);
  // Set Mollie.nl username and password
  $sms->setLogin('dylandejonge', 'gekkebrabo1');
  // Set afzender van het bericht
  $sms->setOriginator('Goedbezig');
  // Add ontvanger(s) van het bericht
  $sms->addRecipients('316'.($_POST["telnummer"]));
  // Add reference (needed for delivery reports)
  $sms->setReference('1234');
  $name = 'Dylan';
  $achievementName = 'Poseidon';
  // Send the SMS Message
  $sms->sendSMS('Gefeliciteerd '.$name.', je hebt de achievement '.$achievementName.' behaald!');

  if ($sms->getSuccess()) {
    echo '<b>SMS message is sent to '.$sms->getSuccessCount().' number(s)!</b>';
  }
  else {
    echo '<b>Sending the message has failed!</b><br>
        Errorcode: ' . $sms->getResultCode() . '<br>
        Errormessage: ' . $sms->getResultMessage();
  }
print_r($sms);
?>

