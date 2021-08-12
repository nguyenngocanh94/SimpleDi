<?php


namespace SimpleDi\Registry;

/**
 * Class Registry
 * @package SimpleDi\Registry
 */
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
     * binding a clouse
     * only binding singleton
     * @param string $source
     * @param string $target
     */
    function bindingClosure(string $source, string $target){
        $this->registerArr = array_merge($this->registerArr, [$source => [$target, SimpleDi::SINGLETON]]);
    }

}