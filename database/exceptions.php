<?php

class AccessoNonAutorizzatoException extends Exception
{
    public function errorMessage()
    {
        return 'Accesso non autorizzato';
    }
}

class AccessoNonAutorizzatoLoginException extends Exception
{
    public function errorMessage()
    {
        return 'Credenziali errate';
    }
}

class MetodoHttpErratoException extends Exception
{
    public function errorMessage()
    {
        return 'Metodo HTTP non supportato';
    }
}

class ErroreServerException extends Exception
{
    public $message = "";

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function errorMessage()
    {
        return $this->message;
    }
}
