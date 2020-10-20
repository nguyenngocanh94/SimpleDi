<?php


namespace SimpleDi;


use SimpleDi\Common\StringUtils;
use SimpleDi\Exceptions\NotRegisterException;
use SimpleDi\Registry\AutoResolver;
use SimpleDi\Registry\SimpleDi;



class App
{
    /**
     * call to get an instance of a class registered.
     * @param string $class
     * @return object
     * @throws Exceptions\NotInitialSimpleDiException
     * @throws NotRegisterException
     */
    public static function resolver(string $class) : object{
        if (StringUtils::endsWith($class, 'Controller')){
            $instance = self::getRegisterInstance($class, SimpleDi::SINGLETON);
        }else{
            $isRegister = SimpleDi::getRegistry()->isRegister($class);
            if ($isRegister){
                list($instance, $scope) = SimpleDi::getRegistry()->getBindingScope($class);
                if ($scope == SimpleDi::SINGLETON && is_object($instance)){
                    return $instance;
                }
                $instance = self::getRegisterInstance($class, $scope);
            }else{
                throw new NotRegisterException('not found class '.$class.' is not mark as @Export');
            }
        }

        return $instance;
    }

    /**
     * @param string $class
     * @param string $scope
     * @return object
     * @throws NotRegisterException
     */
    private static function getRegisterInstance(string $class, string $scope) : object{
        try {
            $dependencies = self::getDependencies($class);
        } catch (ReflectionException $e) {
            l('can resolver the class '.$class);
        }
        $dependencyInstances = AutoResolver::resolve($dependencies);
        if (count($dependencyInstances)>0){
            $instance =  new $class(...$dependencyInstances);
        }else{
            $instance = new $class();
            if ($scope == SINGLETON){
                SimpleDi::getRegistry()->binding($class, $instance);
            }
        }

        return $instance;
    }

    /**
     * get list parameter
     * @param string $class
     * @return array
     * @throws ReflectionException
     */
    private static function getDependencies(string $class): array
    {
        $clazz = new \ReflectionClass($class);
        if (! $clazz->isInstantiable()) {
            return [];
        }

        $dependencies = [];
        $constructor = $clazz->getConstructor();
        if ($constructor==null){
            return $dependencies;
        }
        $params = $constructor->getParameters();
        foreach ($params as $param) {
            $parameterType = (string)$param->getType();
            if (class_exists($parameterType) || interface_exists($parameterType)){
                $dependencies[] = $parameterType;
            }
        }

        return $dependencies;
    }
}