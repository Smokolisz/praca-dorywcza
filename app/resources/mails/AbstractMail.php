<?php

namespace App\Resources\Mails;

abstract class AbstractMail
{
    public abstract function getSubject() : string;
    public abstract function getBody() : string;
}
