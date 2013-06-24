<?php

require_once 'Message.php';

class EmailMessage extends Message
{
    public $recipient;
    public $body;
    public $origin = 'GoedBezig <noreply@marijnvdwerf.nl>';

    /**
     * @param $user Model_User
     * @param $messageType string
     * @param $data mixed
     */
    public function  __construct($user, $messageType, $data)
    {
        $this->recipient = $user->email;

        switch ($messageType) {
            case 'achievement-earned':
                $achievement = $data;
                $this->body = 'Gefeliciteerd ' . $user->name . ', je hebt een achievement vrijgespeeld. ' . $achievement->earnMessage;
                break;
        }
    }

    public function send()
    {
        var_dump([$this->recipient, "GoedBezig", $this->body, "From: " . $this->origin]);
        var_dump(mail($this->recipient, "GoedBezig", $this->body, "From: " . $this->origin));
    }
}
