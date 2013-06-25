<?php

require_once 'Message.php';
require_once 'vendor/class.mollie.php';

class TextMessage extends Message
{
    public $recipient;
    public $body;
    public $origin = 'GoedBezig';

    /**
     * @param $user Model_User
     * @param $messageType string
     * @param $data mixed
     */
    public function  __construct($user, $messageType, $data)
    {
        $this->recipient = $user->phone;

        switch ($messageType) {
            case 'achievement-earned':
                $achievement = $data;
                $this->body = 'Gefeliciteerd ' . $user->name . ', je hebt een achievement vrijgespeeld. Check http://' . $_SERVER['SERVER_NAME'] . '#c=' . $achievement->id;
                break;
        }
    }

    public function send()
    {
        //throw new Exception('Message sending unimplemented');


        // or php4: require('classes/class.mollie-php4.php');
        $sms = new mollie();

        // Choose SMS gateway
        $sms->setGateway(2);
        // Set Mollie.nl username and password
        $sms->setLogin('dylandejonge', 'gekkebrabo1');
        // Set afzender van het bericht
        $sms->setOriginator($this->origin);
        // Add ontvanger(s) van het bericht
        $sms->addRecipients($this->recipient);
        // Add reference (needed for delivery reports)
        $sms->setReference('1234');

        // Send the SMS Message
        $sms->sendSMS($this->body);

        if ($sms->getSuccess()) {
            //echo '<b>SMS message is sent to ' . $sms->getSuccessCount() . ' number(s)!</b>';
        } else {
            throw new Exception($sms->getResultMessage(), $sms->getResultCode());
            //echo '<b>Sending the message has failed!</b><br>
            //Errorcode: ' . $sms->getResultCode() . '<br>
            //Errormessage: ' . $sms->getResultMessage();
        }
        print_r($sms);

    }
}
