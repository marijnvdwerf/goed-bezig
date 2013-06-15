<?php

require_once 'Message.php';

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
                $this->body = 'Gefeliciteerd ' . $user->name . ', je hebt een achievement vrijgespeeld. ' . $achievement->earnMessage;
                break;
        }
    }

    public function send()
    {
        throw new Exception('Message sending unimplemented');
    }
}
