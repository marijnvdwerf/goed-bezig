<?php

abstract class Message
{
    /**
     * @param $user Model_User
     * @param $messageType string
     * @param $data mixed
     */
    abstract public function  __construct($user, $messageType, $data);

    abstract public function send();
}
