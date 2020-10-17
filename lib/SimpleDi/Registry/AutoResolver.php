<?php


namespace SimpleDi\Registry;


use SimpleDi\Exceptions\NotRegisterException;

class AutoResolver
{
    /**
     * @param array $items
     * @return array
     * @throws NotRegisterException
     */
    public static function resolve(array $items): array
    {
        $container = SimpleDi::getInstance()->container;
        $registry = SimpleDi::getInstance()->registry;
        $dependencyInstances = [];
        foreach ($items as $className){
            $instance = $container->resolver($className);
            if ($instance!=null){
                $dependencyInstances[] = $instance;
            }else{
                // need remove in future.
                // so just ignore it.
                if (is_subclass_of($className, Sand_Singleton::class)){
                    $instance = call_user_func(array($className, 'getInstance'));
                    $container->register($className, $instance);
                    $dependencyInstances[] = $instance;
                }else{
                    //must be in Registry
                    if ($registry->isRegister($className)){
                        list($bindClass, $scope) = $registry->getBindingScope($className);
                        if ($scope==TRANSIENT){
                            $dependencyInstances = App::resolver($bindClass);
                        }else{
                            // if container save the instance, drop it out.
                            if (is_object($bindClass)){
                                $instance = $bindClass;
                            }else{
                                $instance = App::resolver($bindClass);
                                $container->register($className, $instance);
                            }

                            $dependencyInstances[] = $instance;
                        }
                    }else{
                        throw new NotRegisterException('not found class '.$className.' is not mark as @Export');
                    }
                }
            }
        }

        return $dependencyInstances;
    }
}