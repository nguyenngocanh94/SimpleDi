<?php


namespace SimpleDi\Registry;


class Registry
{
    private array $registerArr;

    function isRegister(string $class) : bool
    {
        return isset($this->registerArr[$class]);
    }

    function getBindingScope(string $class) : array{
        return $this->registerArr[$class];
    }

    function __construct(array $bindingArray)
    {
        $this->registerArr = $bindingArray;
    }

    /**
     * only binding singleton
     * @param string $source
     * @param object $target
     */
    function binding(string $source, object $target){
        $this->registerArr = array_merge($this->registerArr, [$source => [$target, SINGLETON]]);
    }
}