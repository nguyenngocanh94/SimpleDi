<?php


namespace SimpleDi\Exceptions;


class NotInitialSimpleDiException extends \Exception
{
    protected $message = "SimpleDI have not yet build. call SimpleDi::build(array config) before start application";
}