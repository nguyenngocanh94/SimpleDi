<?php


namespace SimpleDi;


use ReflectionException;
use SimpleDi\Annotations\AnnotationReader;
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
     * @throws ReflectionException|Exceptions\NotInitialSimpleDiException
     */
    private static function getRegisterInstance(string $class, string $scope) : object{
        // in container allway keep singleton
        $isInContainer = SimpleDi::getContainer()->has($class);
        if ($isInContainer){
            return SimpleDi::getContainer()->resolver($class);
        }
        $constructorDependencies = self::getConstructorDependencies($class);
        $dependencyInstances = AutoResolver::resolve($constructorDependencies);
        if (count($dependencyInstances)>0){
            $instance =  new $class(...$dependencyInstances);
        }else{
            $instance = new $class();
        }

        $instance = self::getAndSetInjectDependencies($class, $instance);
        if ($scope == SimpleDi::SINGLETON){
            SimpleDi::getContainer()->register($class, $instance);
        }

        return $instance;
    }

    /**
     * get list parameter
     * @param string $class
     * @return array
     * @throws ReflectionException
     */
    private static function getConstructorDependencies(string $class): array
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

    /**
     * @param string $class
     * @param object $instance
     * @return object
     * @throws Exceptions\NotInitialSimpleDiException
     * @throws NotRegisterException
     * @throws ReflectionException
     */
    private static function getAndSetInjectDependencies(string $class, object $instance): object
    {
        $clazz = new \ReflectionClass($class);
        if (! $clazz->isInstantiable()) {
            return $instance;
        }

        $properties = $clazz->getProperties();
        foreach ($properties as $property){
            if (AnnotationReader::isMarkImport($property)){
                $dependency = $property->getType();
                $dependencyInstance = App::resolver($dependency);
                $property->setAccessible(true);
                $property->setValue($instance, $dependencyInstance);
            }
        }

        return $instance;
    }
}