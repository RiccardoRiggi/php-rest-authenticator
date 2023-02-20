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

class OtterGuardianException extends Exception
{
    public $message = "";
    public $status = 500;

    public function __construct($status,$message)
    {
        $this->message = $message;
        $this->status = $status;
    }

    public function getErrorMessage()
    {
        return $this->message;
    }

    public function getStatus()
    {
        return $this->status;
    }
}