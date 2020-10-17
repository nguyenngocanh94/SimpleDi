<?php


namespace SimpleDi\Container;


class SdContainer implements IContainer
{
    private array $resolvedArray;

    public function __construct($array = null)
    {
        if ($array!=null){
            $this->resolvedArray = $array;
        }else{
            $this->resolvedArray = [];
        }
    }

    function register(string $class, $instance) : void
    {
        $this->resolvedArray[$class] = $instance;
    }

    function resolver(string $class)
    {
        return $this->resolvedArray[$class];
    }
}