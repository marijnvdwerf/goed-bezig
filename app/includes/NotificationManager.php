<?php

class NotificationManager
{

    /**
     * @param $message Message
     */
    public function sendMessage($message)
    {
        if ($message !== null) {
            $message->send();
        }
    }
}
